<?php

namespace App\Http\Controllers;

use App\Models\CreditScoringComponent;
use App\Models\Customer;
use App\Models\Evaluation;
use App\Models\EvaluationNotification;
use App\Models\User;
use App\Models\EconomicSector;
use App\Models\NonBankThirdParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CreditScoringService;
class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deletedEvaluations = collect();
        if (auth()->user()->can('restore evaluations')) {
            $deletedEvaluations = Evaluation::onlyTrashed()->with('customer')->get();
        }
        return view('evaluations.index', compact('deletedEvaluations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->cannot('create evaluations')) abort(403);
        $customers = Customer::with(['user', 'evaluations.collaterals', 'evaluations.externalLoans'])->orderBy('id', 'desc')->get();
        // Get Asset Components (Neraca) - Removed as we now use direct columns

        // Get Scoring Components organized by Loan Scheme and then Category
        $scoringComponents = CreditScoringComponent::where('is_active', true)
            ->get()
            ->groupBy(['loan_scheme', 'category']);

        $aoUsers = User::role('AO')->get();
        $economicSectors = EconomicSector::all();
        $nonBankThirdParties = NonBankThirdParty::all();

        return view('evaluations.create', compact('customers', 'scoringComponents', 'aoUsers', 'economicSectors', 'nonBankThirdParties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->cannot('create evaluations')) abort(403);
        // Validation (Expanded to cover new fields)
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'loan_term_months' => 'required|integer',
            'evaluation_date' => 'required|date',
            'loan_scheme' => 'required|string',
            'loan_type' => 'required|string',
            'customer_type' => 'required|string',
            'customer_status' => 'required|string',
            'customer_profile' => 'nullable|string',
            'customer_dependents' => 'nullable|integer',
            'office_branch' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'loan_interest_rate' => 'nullable|numeric',
            'loan_purpose' => 'nullable|string',
            'seasonal_loan_repayment_source' => 'nullable|string',

            // Old Loan Details
            'old_loan_purpose' => 'nullable|string',
            'old_seasonal_loan_repayment_source' => 'nullable|string',
            'old_loan_type' => 'nullable|string',
            'old_loan_amount' => 'nullable|numeric',
            'old_loan_term_months' => 'nullable|integer',
            'old_loan_interest_rate' => 'nullable|numeric',

            // Entrepreneurship Details
            'customer_entreprenuership_legality' => 'nullable|string',
            'customer_entreprenuership_ownership' => 'nullable|string',
            'customer_entreprenuership_name' => 'nullable|string',
            'customer_entreprenuership_type' => 'nullable|string',
            'customer_entreprenuership_year' => 'nullable|integer',
            'customer_entreprenuership_tax_id' => 'nullable|string',
            'customer_entreprenuership_legality_id' => 'nullable|string',
            'customer_entreprenuership_legality_register_id' => 'nullable|string',
            'customer_entreprenuership_description' => 'nullable|string',
            'customer_entreprenuership_products' => 'nullable|string',
            'customer_entreprenuership_place_status' => 'nullable|string',
            'customer_entreprenuership_phone' => 'nullable|string',
            'customer_entreprenuership_employee_count' => 'nullable|string',

            // Company Details
            'customer_company_years' => 'nullable|string',

            'business_legality_photo' => 'nullable|image|max:20480',
            'business_detail_1_photo' => 'nullable|image|max:20480',
            'business_detail_2_photo' => 'nullable|image|max:20480',
            'business_legality_photo_data' => 'nullable|string',
            'business_detail_1_photo_data' => 'nullable|string',
            'business_detail_2_photo_data' => 'nullable|string',
            'location_image' => 'nullable|string',
            'business_latitude' => 'nullable|numeric',
            'business_longitude' => 'nullable|numeric',
            'business_village' => 'nullable|string',
            'business_district' => 'nullable|string',
            'business_regency' => 'nullable|string',
            'business_province' => 'nullable|string',

            'economic_sector' => 'nullable|string',
            'economic_sector_code' => 'nullable|string',
            'non_bank_third_party' => 'nullable|string',
            'non_bank_third_party_code' => 'nullable|string',

            'min_monthly_cash_bank_balance' => 'nullable|numeric',
            'op_opening_balance_before' => 'nullable|numeric',
            'op_opening_balance_after' => 'nullable|numeric',
            'cash_in_salary_before' => 'nullable|numeric',
            'cash_in_salary_after' => 'nullable|numeric',
            'cash_in_business_before' => 'nullable|numeric',
            'cash_in_business_after' => 'nullable|numeric',
            'cash_in_other_before' => 'nullable|numeric',
            'cash_in_other_after' => 'nullable|numeric',
            'other_incomes' => 'nullable|array',
            'other_incomes.*.name' => 'nullable|string',
            'other_incomes.*.before' => 'nullable|numeric',
            'other_incomes.*.after' => 'nullable|numeric',
            'capital_injection_amount' => 'nullable|numeric',
            'loan_duty_stamp_cost' => 'nullable|numeric',
            'loan_notary_public_cost' => 'nullable|numeric',
            'loan_insurance_cost' => 'nullable|numeric',
            'loan_other_cost' => 'nullable|numeric',
            'loan_provision_rate' => 'nullable|numeric',
            'loan_admin_rate' => 'nullable|numeric',
            'interest_rate' => 'nullable|numeric',
            'loan_tenor' => 'nullable|numeric',
            'rpc_ratio' => 'nullable|numeric',
            'rekomendasi_loan_provision_cost' => 'nullable|numeric',
            'rekomendasi_loan_administration_cost' => 'nullable|numeric',
            'rekomendasi_loan_duty_stamp_cost' => 'nullable|numeric',
            'rekomendasi_loan_notary_public_cost' => 'nullable|numeric',
            'rekomendasi_loan_insurance_cost' => 'nullable|numeric',
            'rekomendasi_loan_other_cost' => 'nullable|numeric',

            // Expenses Validation
            'hh_living_before' => 'nullable|numeric',
            'hh_living_after' => 'nullable|numeric',
            'hh_utilities_before' => 'nullable|numeric',
            'hh_utilities_after' => 'nullable|numeric',
            'hh_education_before' => 'nullable|numeric',
            'hh_education_after' => 'nullable|numeric',
            'hh_telecom_before' => 'nullable|numeric',
            'hh_telecom_after' => 'nullable|numeric',
            'hh_transport_before' => 'nullable|numeric',
            'hh_transport_after' => 'nullable|numeric',
            'hh_entertainment_before' => 'nullable|numeric',
            'hh_entertainment_after' => 'nullable|numeric',
            'hh_rent_before' => 'nullable|numeric',
            'hh_rent_after' => 'nullable|numeric',
            'hh_other_before' => 'nullable|numeric',
            'hh_other_after' => 'nullable|numeric',
            'biz_hpp_before' => 'nullable|numeric',
            'biz_hpp_after' => 'nullable|numeric',
            'biz_labor_before' => 'nullable|numeric',
            'biz_labor_after' => 'nullable|numeric',
            'biz_telecom_before' => 'nullable|numeric',
            'biz_telecom_after' => 'nullable|numeric',
            'biz_transport_before' => 'nullable|numeric',
            'biz_transport_after' => 'nullable|numeric',
            'biz_utilities_before' => 'nullable|numeric',
            'biz_utilities_after' => 'nullable|numeric',
            'biz_rent_before' => 'nullable|numeric',
            'biz_rent_after' => 'nullable|numeric',
            'biz_other_before' => 'nullable|numeric',
            'biz_other_after' => 'nullable|numeric',
            'other_expenses_before' => 'nullable|numeric',
            'other_expenses_after' => 'nullable|numeric',

            // Custom Assets
            'custom_assets' => 'nullable|array',
            'custom_assets.*.name' => 'nullable|string',
            'custom_assets.*.type' => 'nullable|string',
            'custom_assets.*.estimated_price' => 'nullable|numeric',

            // New Neraca Fields
            'kas_usaha' => 'nullable|numeric',
            'piutang_usaha' => 'nullable|numeric',
            'persediaan' => 'nullable|numeric',
            'kewajiban_lancar' => 'nullable|numeric',
            'kewajiban_jangka_panjang' => 'nullable|numeric',
            'modal_usaha' => 'nullable|numeric',

            // Document Checklist
            'document_checklist' => 'nullable|json'
        ]);

        DB::beginTransaction();
        try {
            // Recalculate Totals
            $cashInTotalBefore = ($request->cash_in_salary_before ?? 0) + ($request->cash_in_business_before ?? 0) + ($request->cash_in_other_before ?? 0);
            $cashInTotalAfter = ($request->cash_in_salary_after ?? 0) + ($request->cash_in_business_after ?? 0) + ($request->cash_in_other_after ?? 0) + ($request->capital_injection_amount ?? 0);

            $hhTotalBefore = ($request->hh_living_before ?? 0) + ($request->hh_utilities_before ?? 0) + ($request->hh_education_before ?? 0) + ($request->hh_telecom_before ?? 0) + ($request->hh_transport_before ?? 0) + ($request->hh_entertainment_before ?? 0) + ($request->hh_rent_before ?? 0) + ($request->hh_other_before ?? 0);
            $hhTotalAfter = ($request->hh_living_after ?? 0) + ($request->hh_utilities_after ?? 0) + ($request->hh_education_after ?? 0) + ($request->hh_telecom_after ?? 0) + ($request->hh_transport_after ?? 0) + ($request->hh_entertainment_after ?? 0) + ($request->hh_rent_after ?? 0) + ($request->hh_other_after ?? 0);

            $bizTotalBefore = ($request->biz_hpp_before ?? 0) + ($request->biz_labor_before ?? 0) + ($request->biz_telecom_before ?? 0) + ($request->biz_transport_before ?? 0) + ($request->biz_utilities_before ?? 0) + ($request->biz_rent_before ?? 0) + ($request->biz_other_before ?? 0);
            $bizTotalAfter = ($request->biz_hpp_after ?? 0) + ($request->biz_labor_after ?? 0) + ($request->biz_telecom_after ?? 0) + ($request->biz_transport_after ?? 0) + ($request->biz_utilities_after ?? 0) + ($request->biz_rent_after ?? 0) + ($request->biz_other_after ?? 0);

            // Calculate bank installments from external loans
            $totalExternalInstallment = 0;
            if ($request->has('external_loans')) {
                foreach ($request->external_loans as $loan) {
                    $totalExternalInstallment += (float) str_replace('.', '', $loan['installment_amount'] ?? '') ?: 0;
                }
            }
            $bankInstallmentsBefore = ($request->bank_bni_before ?? 0) + $totalExternalInstallment;
            $bankInstallmentsAfter = ($request->bank_bni_after ?? 0) + $totalExternalInstallment;

            $cashOutTotalBefore = $bankInstallmentsBefore + $hhTotalBefore + $bizTotalBefore + ($request->other_expenses_before ?? 0);
            $cashOutTotalAfter = $bankInstallmentsAfter + $hhTotalAfter + $bizTotalAfter + ($request->other_expenses_after ?? 0);

            $netCashFlowBefore = $cashInTotalBefore - $cashOutTotalBefore;
            $netCashFlowAfter = $cashInTotalAfter - $cashOutTotalAfter;

            $endOpBalanceBefore = ($request->op_opening_balance_before ?? 0) + $netCashFlowBefore;
            $endOpBalanceAfter = ($request->op_opening_balance_after ?? 0) + $netCashFlowAfter;

            // Final Credit Realization Cost
            // Rate logic for fallback: 0.5% if tenor < 3 months, else 1%
            $feeRate = ($request->loan_term_months < 3) ? 0.005 : 0.01;

            $loanProvisionCost = $request->loan_provision_cost ?? ($request->loan_amount * $feeRate);
            $loanAdminCost = $request->loan_administration_cost ?? ($request->loan_amount * $feeRate);
            $loanTotalRealizationCost = $loanProvisionCost + $loanAdminCost +
                ($request->loan_duty_stamp_cost ?? 0) +
                ($request->loan_notary_public_cost ?? 0) +
                ($request->loan_insurance_cost ?? 0) +
                ($request->loan_other_cost ?? 0);

            // Proposed Installment
            $interestRate = $request->loan_interest_rate ?? $request->interest_rate ?? 12;
            $tenor = $request->loan_term_months ?? $request->loan_tenor ?? 11;

            $monthlyInterest = round(($request->loan_amount * (($interestRate) / 100)) / 12);

            // If Seasonal, principal is not paid monthly
            if ($request->loan_type === 'Pinjaman Musiman') {
                $monthlyPrincipal = 0;
            }
            else {
                $monthlyPrincipal = $request->loan_amount / ($tenor > 0 ? $tenor : 1);
            }

            $monthlyInstallment = round($monthlyInterest + $monthlyPrincipal);

            // Final Balances
            $cashBankTotalBefore = $endOpBalanceBefore - $loanTotalRealizationCost + ($request->loan_amount ?? 0);
            $cashBankTotalAfter = $endOpBalanceAfter - $monthlyInstallment;

            $loanRemBalanceBefore = $request->loan_amount ?? 0;
            $loanRemBalanceAfter = $loanRemBalanceBefore - $monthlyPrincipal;

            $evaluationData = [
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
                'application_id' => 'BPRPURI/' . date('Ymd') . '/' . strtoupper(bin2hex(random_bytes(3))),
                'evaluation_date' => $request->evaluation_date,
                'loan_scheme' => $request->loan_scheme,
                'loan_type' => $request->loan_type,
                'loan_purpose' => $request->loan_purpose,
                'seasonal_loan_repayment_source' => $request->seasonal_loan_repayment_source,

                // Old Loan Details
                'old_loan_purpose' => $request->old_loan_purpose,
                'old_seasonal_loan_repayment_source' => $request->old_seasonal_loan_repayment_source,
                'old_loan_type' => $request->old_loan_type,
                'old_loan_amount' => $request->old_loan_amount,
                'old_loan_term_months' => $request->old_loan_term_months,
                'old_loan_interest_rate' => $request->old_loan_interest_rate,

                'customer_type' => $request->customer_type,
                'customer_status' => $request->customer_status,
                'loan_amount' => $request->loan_amount,
                'loan_term_months' => $tenor,
                'loan_interest_rate' => $interestRate,
                'customer_profile' => $request->customer_profile ?? '-',
                'customer_dependents' => $request->customer_dependents,
                'economic_sector' => $request->economic_sector ?? '-',
                'economic_sector_code' => $request->economic_sector_code ?? '-',
                'non_bank_third_party' => $request->non_bank_third_party ?? '-',
                'non_bank_third_party_code' => $request->non_bank_third_party_code ?? '-',
                'customer_employment_status' => $request->customer_employment_status,
                'customer_employee_status' => $request->customer_employee_status,
                'customer_company_sector' => $request->customer_company_sector,
                'customer_company_employee_count' => $request->customer_company_employee_count,
                'customer_company_salary_frequency' => $request->customer_company_salary_frequency,
                'customer_company_payday' => $request->customer_company_payday,
                'customer_company_name' => $request->customer_company_name,
                'customer_company_address' => $request->customer_company_address,
                'customer_company_phone' => $request->customer_company_phone,
                'customer_company_position' => $request->customer_company_position,
                'customer_entrepreneurship_status' => $request->customer_entrepreneurship_status,
                'customer_entreprenuership_type' => $request->customer_entreprenuership_type,
                'customer_entreprenuership_name' => $request->customer_entreprenuership_name,
                'customer_entreprenuership_legality' => $request->customer_entreprenuership_legality,
                'customer_entreprenuership_ownership' => $request->customer_entreprenuership_ownership,
                'customer_entreprenuership_year' => $request->customer_entreprenuership_year,
                'customer_entreprenuership_tax_id' => $request->customer_entreprenuership_tax_id,
                'customer_entreprenuership_legality_id' => $request->customer_entreprenuership_legality_id,
                'customer_entreprenuership_legality_register_id' => $request->customer_entreprenuership_legality_register_id,
                'customer_entreprenuership_description' => $request->customer_entreprenuership_description,
                'customer_entreprenuership_products' => $request->customer_entreprenuership_products,
                'customer_entreprenuership_place_status' => $request->customer_entreprenuership_place_status,
                'customer_entreprenuership_phone' => $request->customer_entreprenuership_phone,
                'customer_entreprenuership_employee_count' => $request->customer_entreprenuership_employee_count,
                'customer_company_years' => $request->customer_company_years,

                // Geolocation & Address
                'business_latitude' => $request->business_latitude,
                'business_longitude' => $request->business_longitude,
                'business_village' => $request->business_village,
                'business_district' => $request->business_district,
                'business_regency' => $request->business_regency,
                'business_province' => $request->business_province,

                // Cash Flow Analysis Fields
                'min_monthly_cash_bank_balance' => $request->min_monthly_cash_bank_balance ?? 0,
                'op_opening_balance_before' => $request->op_opening_balance_before ?? 0,
                'op_opening_balance_after' => $request->op_opening_balance_after ?? 0,
                'cash_in_salary_before' => $request->cash_in_salary_before ?? 0,
                'cash_in_salary_after' => $request->cash_in_salary_after ?? 0,
                'cash_in_business_before' => $request->cash_in_business_before ?? 0,
                'cash_in_business_after' => $request->cash_in_business_after ?? 0,
                'cash_in_other_before' => $request->cash_in_other_before ?? 0,
                'cash_in_other_after' => $request->cash_in_other_after ?? 0,
                'cash_in_other_details' => $request->has('other_incomes') ? json_encode($request->other_incomes) : null,
                'capital_injection_amount' => $request->capital_injection_amount ?? 0,
                'loan_duty_stamp_cost' => $request->loan_duty_stamp_cost ?? 0,

                // Calculated Totals (Cash In)
                'cash_in_total_before' => $cashInTotalBefore,
                'cash_in_total_after' => $cashInTotalAfter,

                // Detailed Expenses
                'other_bank_installments_before' => $bankInstallmentsBefore,
                'other_bank_installments_after' => $bankInstallmentsAfter,

                'hh_living_before' => $request->hh_living_before ?? 0,
                'hh_living_after' => $request->hh_living_after ?? 0,
                'hh_utilities_before' => $request->hh_utilities_before ?? 0,
                'hh_utilities_after' => $request->hh_utilities_after ?? 0,
                'hh_education_before' => $request->hh_education_before ?? 0,
                'hh_education_after' => $request->hh_education_after ?? 0,
                'hh_telecom_before' => $request->hh_telecom_before ?? 0,
                'hh_telecom_after' => $request->hh_telecom_after ?? 0,
                'hh_transport_before' => $request->hh_transport_before ?? 0,
                'hh_transport_after' => $request->hh_transport_after ?? 0,
                'hh_entertainment_before' => $request->hh_entertainment_before ?? 0,
                'hh_entertainment_after' => $request->hh_entertainment_after ?? 0,
                'hh_rent_before' => $request->hh_rent_before ?? 0,
                'hh_rent_after' => $request->hh_rent_after ?? 0,
                'hh_other_before' => $request->hh_other_before ?? 0,
                'hh_other_after' => $request->hh_other_after ?? 0,

                'biz_hpp_before' => $request->biz_hpp_before ?? 0,
                'biz_hpp_after' => $request->biz_hpp_after ?? 0,
                'biz_labor_before' => $request->biz_labor_before ?? 0,
                'biz_labor_after' => $request->biz_labor_after ?? 0,
                'biz_telecom_before' => $request->biz_telecom_before ?? 0,
                'biz_telecom_after' => $request->biz_telecom_after ?? 0,
                'biz_transport_before' => $request->biz_transport_before ?? 0,
                'biz_transport_after' => $request->biz_transport_after ?? 0,
                'biz_utilities_before' => $request->biz_utilities_before ?? 0,
                'biz_utilities_after' => $request->biz_utilities_after ?? 0,
                'biz_rent_before' => $request->biz_rent_before ?? 0,
                'biz_rent_after' => $request->biz_rent_after ?? 0,
                'biz_other_before' => $request->biz_other_before ?? 0,
                'biz_other_after' => $request->biz_other_after ?? 0,

                'other_expenses_before' => $request->other_expenses_before ?? 0,
                'other_expenses_after' => $request->other_expenses_after ?? 0,

                // Final Summary Totals
                'hh_total_before' => $hhTotalBefore,
                'hh_total_after' => $hhTotalAfter,
                'biz_total_before' => $bizTotalBefore,
                'biz_total_after' => $bizTotalAfter,
                'cash_out_total_before' => $cashOutTotalBefore,
                'cash_out_total_after' => $cashOutTotalAfter,
                'net_cash_flow_before' => $netCashFlowBefore,
                'net_cash_flow_after' => $netCashFlowAfter,
                'end_op_balance_before' => $endOpBalanceBefore,
                'end_op_balance_after' => $endOpBalanceAfter,

                // BPR Realization Costs
                'loan_provision_cost' => $loanProvisionCost,
                'loan_administration_cost' => $loanAdminCost,
                'loan_provision_rate' => $request->loan_provision_rate,
                'loan_admin_rate' => $request->loan_admin_rate,

                'loan_notary_public_cost' => $request->loan_notary_public_cost ?? 0,
                'loan_insurance_cost' => $request->loan_insurance_cost ?? 0,
                'loan_other_cost' => $request->loan_other_cost ?? 0,
                'loan_total_cost' => $loanTotalRealizationCost,

                // Rekomendasi Costs
                'rekomendasi_loan_provision_cost' => $request->rekomendasi_loan_provision_cost ?? 0,
                'rekomendasi_loan_administration_cost' => $request->rekomendasi_loan_administration_cost ?? 0,
                'rekomendasi_loan_duty_stamp_cost' => $request->rekomendasi_loan_duty_stamp_cost ?? 0,
                'rekomendasi_loan_notary_public_cost' => $request->rekomendasi_loan_notary_public_cost ?? 0,
                'rekomendasi_loan_insurance_cost' => $request->rekomendasi_loan_insurance_cost ?? 0,
                'rekomendasi_loan_other_cost' => $request->rekomendasi_loan_other_cost ?? 0,

                // RPC Ratio
                'rpc_ratio' => $request->rpc_ratio,

                // Proposed Installment details
                'installment_proposed_rate' => $interestRate,
                'installment_proposed_term' => $tenor,
                'installment_proposed_total' => $monthlyInstallment,
                'installment_proposed_interest' => $monthlyInterest,
                'installment_proposed_principal' => $monthlyPrincipal,

                // Final Balances
                'cash_bank_total_before' => $cashBankTotalBefore,
                'cash_bank_total_after' => $cashBankTotalAfter,
                'loan_rem_balance_before' => $loanRemBalanceBefore,
                'loan_rem_balance_after' => $loanRemBalanceAfter,

                // Financial ratios from form
                'roi_percent' => $request->roi_percent ?? 0,
                'roe_percent' => $request->roe_percent ?? 0,
                'dscr' => $request->dscr ?? 0,
                'debt_to_income_ratio' => $request->dti_percent ?? 0,
                'net_profit_margin' => $request->pm_percent ?? 0,

                // Manual fields for now
                'installment_proposed_amount' => $request->loan_installment ?? 0,

                // Neraca - Aktiva (Assets)
                'gold_before' => $request->gold_before ?? 0,
                'gold_after' => $request->gold_after ?? 0,
                'receivables_before' => $request->receivables_before ?? 0,
                'receivables_after' => $request->receivables_after ?? 0,
                'other_assets_before' => $request->other_assets_before ?? 0,
                'other_assets_after' => $request->other_assets_after ?? 0,

                // Neraca - Pasiva (Liabilities)
                'liab_third_party_before' => $request->liab_third_party_before ?? 0,
                'liab_third_party_after' => $request->liab_third_party_after ?? 0,
                'liab_bpr_before' => $request->liab_bpr_before ?? 0,
                'liab_bpr_after' => $request->liab_bpr_after ?? 0,
                'liab_other_before' => $request->liab_other_before ?? 0,
                'liab_other_after' => $request->liab_other_after ?? 0,

                // Neraca - Modal (Equity & Profit)
                'equity_own_before' => $request->equity_own_before ?? 0,
                'equity_own_after' => $request->equity_own_after ?? 0,
                'profit_current_before' => $request->profit_current_before ?? 0,
                'profit_current_after' => $request->profit_current_after ?? 0,
                'profit_past_before' => $request->profit_past_before ?? 0,
                'profit_past_after' => $request->profit_past_after ?? 0,

                // New Neraca Fields
                'kas_usaha' => $request->kas_usaha ?? 0,
                'piutang_usaha' => $request->piutang_usaha ?? 0,
                'persediaan' => $request->persediaan ?? 0,
                'kewajiban_lancar' => $request->kewajiban_lancar ?? 0,
                'kewajiban_jangka_panjang' => $request->kewajiban_jangka_panjang ?? 0,
                'modal_usaha' => $request->modal_usaha ?? 0,

                // Character Scoring (Part 6)
                'char_credit_bureau' => $request->char_credit_bureau ?? 0,
                'char_info_consistency' => $request->char_info_consistency ?? 0,
                'char_relationship' => $request->char_relationship ?? 0,
                'char_stability' => $request->char_stability ?? 0,
                'char_reputation' => $request->char_reputation ?? 0,
                'char_total_score' => $request->char_total_score ?? 0,

                // Capacity Scoring (Part 6 continued)
                'cap_rpc' => $request->cap_rpc ?? 0,
                'cap_lama_usaha' => $request->cap_lama_usaha ?? 0,
                'cap_usia' => $request->cap_usia ?? 0,
                'cap_pengelolaan' => $request->cap_pengelolaan ?? 0,
                'cap_total_score' => $request->cap_total_score ?? 0,

                // Capital Scoring
                'capital_dar' => $request->capital_dar ?? 0,
                'capital_der' => $request->capital_der ?? 0,
                'capital_total_score' => $request->capital_total_score ?? 0,

                // Condition Scoring
                'cond_lokasi' => $request->cond_lokasi ?? 0,
                'cond_profit' => $request->cond_profit ?? 0,
                'cond_dscr' => $request->cond_dscr ?? 0,
                'condition_total_score' => $request->condition_total_score ?? 0,

                // Collateral Scoring
                'col_kepemilikan' => $request->col_kepemilikan ?? 0,
                'col_peruntukan' => $request->col_peruntukan ?? 0,
                'col_lebar_jalan' => $request->col_lebar_jalan ?? 0,
                'col_coverage' => $request->col_coverage ?? 0,
                'col_marketable' => $request->col_marketable ?? 0,
                'col_total_score' => $request->col_total_score ?? 0,

                // Document Checklist
                'document_checklist' => $request->has('document_checklist') ? json_decode($request->document_checklist, true) : null,
            ];

            // Compute Final Score centrally via Service
            $creditScoringService = new CreditScoringService();
            $scoringResult = $creditScoringService->calculateScores($evaluationData);
            $evaluationData['final_score'] = $scoringResult['final_score'];

            // Handle Photo Uploads
            $photoFields = [
                'business_legality_photo' => 'business_legality_path',
                'business_detail_1_photo' => 'business_detail_1_path',
                'business_detail_2_photo' => 'business_detail_2_path',
            ];

            foreach ($photoFields as $inputName => $columnName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = time() . '_' . $inputName . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('evaluations/photos', $filename, 'local');
                    $evaluationData[$columnName] = $filename;
                } elseif (!empty($request->{$inputName . '_data'})) {
                    // Fallback: decode base64 data (from validation failure re-submission)
                    $base64Data = $request->{$inputName . '_data'};
                    $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
                    $base64Data = str_replace(' ', '+', $base64Data);
                    $filename = time() . '_' . $inputName . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('local')->put('evaluations/photos/' . $filename, base64_decode($base64Data));
                    $evaluationData[$columnName] = $filename;
                }
            }

            // Handle Map Image (Base64)
            Log::info('Checking location_image', ['has_image' => !empty($request->location_image), 'length' => strlen($request->location_image ?? '')]);
            if (!empty($request->location_image)) {
                $image = $request->location_image;
                $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'map_' . time() . '_' . uniqid() . '.png';
                $locationImagePath = 'evaluations/map/' . $imageName;
                \Illuminate\Support\Facades\Storage::disk('local')->put($locationImagePath, base64_decode($image));
                $evaluationData['business_location_image_path'] = $imageName; // Store only filename to match serve logic
            }

            // Calculate Path Distance
            if (!empty($evaluationData['business_latitude']) && !empty($evaluationData['business_longitude'])) {
                $evaluationData['path_distance'] = $this->calculateDistance(
                    $evaluationData['business_latitude'],
                    $evaluationData['business_longitude'],
                    -7.487391381663846,
                    112.44006721604295
                );
            }

            // Create Evaluation
            $evaluation = Evaluation::create($evaluationData);

        // 2. Save Assets (Neraca) - Moved to main evaluations table

            // 2.5 Save Custom Assets
            if ($request->has('custom_assets')) {
                foreach ($request->custom_assets as $customAsset) {
                    if (empty($customAsset['name']) && empty($customAsset['type'])) {
                        continue;
                    }
                    $evaluation->customAssets()->create([
                        'name' => $customAsset['name'] ?? '-',
                        'type' => $customAsset['type'] ?? '-',
                        'estimated_price' => str_replace('.', '', $customAsset['estimated_price'] ?? '') ?: 0,
                    ]);
                }
            }

            // 2.6 Save Guarantors (Penjamin)
            if ($request->has('guarantors')) {
                foreach ($request->guarantors as $guarantor) {
                    if (empty($guarantor['name'])) {
                        continue;
                    }
                    $evaluation->guarantors()->create([
                        'name' => $guarantor['name'],
                        'relationship' => $guarantor['relationship'] ?? '-',
                    ]);
                }
            }

            // 3. Save External Loans (SLIK)
            if ($request->has('external_loans')) {
                foreach ($request->external_loans as $loan) {
                    // Sanitize numeric fields (remove dots)
                    $loan['original_amount'] = str_replace('.', '', $loan['original_amount'] ?? '') ?: 0;
                    $loan['outstanding_balance'] = str_replace('.', '', $loan['outstanding_balance'] ?? '') ?: 0;
                    $loan['installment_amount'] = str_replace('.', '', $loan['installment_amount'] ?? '') ?: 0;

                    $evaluation->externalLoans()->create($loan);
                }
            }

            // 4. Save Collaterals
            if ($request->has('collaterals')) {
                foreach ($request->collaterals as $index => $collateralData) {
                    // 1. Prepare Base Data
                    $dbData = [
                        'type' => $collateralData['type'] ?? 'unknown',
                        'owner_name' => $collateralData['owner_name'] ?? '-',
                        'owner_ktp' => $collateralData['owner_ktp'] ?? null,
                        'proof_type' => $collateralData['proof_type'] ?? '-',
                        'proof_number' => $collateralData['proof_number'] ?? '-',
                        'market_value' => str_replace('.', '', $collateralData['market_value'] ?? '') ?: 0,
                        'bank_value' => str_replace('.', '', $collateralData['bank_value'] ?? '') ?: 0,
                        'location_address' => $collateralData['location_address'] ?? null,
                        'latitude' => $collateralData['latitude'] ?? null,
                        'longitude' => $collateralData['longitude'] ?? null,
                        'village' => $collateralData['village'] ?? null,
                        'district' => $collateralData['district'] ?? null,
                        'regency' => $collateralData['regency'] ?? null,
                        'province' => $collateralData['province'] ?? null,
                    ];

                    // Calculate path distance for this collateral
                    if (!empty($dbData['latitude']) && !empty($dbData['longitude'])) {
                        $dbData['path_distance'] = $this->calculateDistance(
                            $dbData['latitude'],
                            $dbData['longitude'],
                            -7.487391381663846,
                            112.44006721604295
                        );
                    }

                    // 2. Map Type-Specific Fields
                    if ($dbData['type'] === 'vehicle') {
                        $dbData['vehicle_brand'] = $collateralData['brand'] ?? '';
                        $dbData['vehicle_model'] = $collateralData['model'] ?? '';
                        $dbData['vehicle_year'] = $collateralData['year'] ?? null;
                        $dbData['vehicle_color'] = $collateralData['color'] ?? null;
                        $dbData['vehicle_plate_number'] = $collateralData['police_number'] ?? null;
                        $dbData['vehicle_frame_number'] = $collateralData['chassis_number'] ?? null;
                        $dbData['vehicle_engine_number'] = $collateralData['engine_number'] ?? null;
                    }
                    elseif ($dbData['type'] === 'certificate') {
                        $dbData['property_surface_area'] = $collateralData['land_area'] ?? 0;
                        $dbData['property_building_area'] = $collateralData['building_area'] ?? 0;
                        $dbData['property_address'] = $collateralData['location_address'] ?? null;
                        
                        $dbData['peruntukan_tanah'] = $collateralData['peruntukan_tanah'] ?? null;
                        $dbData['lebar_jalan'] = $collateralData['lebar_jalan'] ?? null;
                        $dbData['kondisi_bangunan'] = $collateralData['kondisi_bangunan'] ?? null;
                        $dbData['material_pondasi'] = $collateralData['material_pondasi'] ?? null;
                        $dbData['material_tembok'] = $collateralData['material_tembok'] ?? null;
                        $dbData['material_atap'] = $collateralData['material_atap'] ?? null;
                        $dbData['material_kusen'] = $collateralData['material_kusen'] ?? null;
                        $dbData['material_daun_pintu'] = $collateralData['material_daun_pintu'] ?? null;
                    }

                    // 3. Handle Image Uploads
                    if ($request->hasFile("collaterals.$index.images")) {
                        $images = $request->file("collaterals.$index.images");

                        foreach ($images as $imgIdx => $imageFile) {
                            $filename = 'col_' . time() . '_' . $index . '_' . $imgIdx . '.' . $imageFile->getClientOriginalExtension();
                            $imageFile->storeAs('evaluations/collaterals', $filename, 'local');

                            // Map index to column
                            if ($dbData['type'] === 'certificate') {
                                // 0: Sertifikat, 1: PBB, 2: IMB, 3: Foto Lokasi
                                $colName = 'property_image_' . ($imgIdx + 1);
                                if ($imgIdx == 3)
                                    $dbData['image_proof'] = $filename; // Saving location/proof duplicate if needed, or just specific column
                                $dbData[$colName] = $filename;
                            }
                            else {
                                // 0: Foto BPKB, 1: Foto Kendaraan
                                $colName = 'vehicle_image_' . ($imgIdx + 1);
                                if ($imgIdx == 0)
                                    $dbData['image_proof'] = $filename;
                                $dbData[$colName] = $filename;
                            }
                        }
                    }

                    // 3b. Fallback: Handle base64 image data (from validation failure re-submission)
                    if (!empty($collateralData['images_data'])) {
                        foreach ($collateralData['images_data'] as $imgIdx => $base64Data) {
                            if (empty($base64Data) || strlen($base64Data) < 50) continue;

                            // Skip if file upload already handled this index
                            $checkColName = ($dbData['type'] === 'certificate')
                                ? 'property_image_' . ($imgIdx + 1)
                                : 'vehicle_image_' . ($imgIdx + 1);
                            if (!empty($dbData[$checkColName])) continue;

                            // Decode base64
                            $cleanData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
                            $cleanData = str_replace(' ', '+', $cleanData);
                            $filename = 'col_' . time() . '_' . $index . '_' . $imgIdx . '.jpg';
                            \Illuminate\Support\Facades\Storage::disk('local')->put('evaluations/collaterals/' . $filename, base64_decode($cleanData));

                            if ($dbData['type'] === 'certificate') {
                                $colName = 'property_image_' . ($imgIdx + 1);
                                if ($imgIdx == 3) $dbData['image_proof'] = $filename;
                                $dbData[$colName] = $filename;
                            } else {
                                $colName = 'vehicle_image_' . ($imgIdx + 1);
                                if ($imgIdx == 0) $dbData['image_proof'] = $filename;
                                $dbData[$colName] = $filename;
                            }
                        }
                    }

                    $evaluation->collaterals()->create($dbData);
                }
            }

            // 5. Save 5C Scores
            if ($request->has('scores')) {
                foreach ($request->scores as $componentId => $scoreData) {
                    $score = $scoreData['score'] ?? 0;
                    $weight = CreditScoringComponent::find($componentId)->weight ?? 0;
                    $weightedValue = ($score * $weight) / 100;

                    $evaluation->scores()->create([
                        'credit_scoring_component_id' => $componentId,
                        'score' => $score,
                        'weight' => $weight,
                        'weighted_value' => $weightedValue,
                        'note' => $scoreData['note'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil ditambahkan.');

        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Evaluation Store Error: ' . $e->getMessage());
            return back()->with('error', 'Error creating evaluation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluation $evaluation)
    {
        $evaluation->load([
            'customer',
            'user',
            'scores.component',
            'collaterals',
            'externalLoans'
        ]);

        return view('evaluations.show', compact('evaluation'));
    }

    /**
     * Print the specified resource.
     */
    public function print(Evaluation $evaluation)
    {
        $evaluation->load([
            'customer',
            'user',
            'scores.component',
            'collaterals',
            'customAssets',
            'externalLoans',
            'guarantors'
        ]);

        $creditScoringService = new CreditScoringService();
        $scoringResult = $creditScoringService->calculateScores($evaluation);

        return view('evaluations.print', compact('evaluation', 'scoringResult'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evaluation $evaluation)
    {
        if ($evaluation->approval_status !== 'draft') {
            if (auth()->user()->can('approve evaluations')) {
                $evaluation->load([
                    'customer',
                    'collaterals',
                    'externalLoans',
                    'scores',
                    'guarantors'
                ]);
            } else {
                return redirect()->route('evaluations.index')
                    ->with('error', 'Evaluasi harus ditarik kembali (revoke) terlebih dahulu sebelum dapat diedit.');
            }
        }

        // load relationships once, after the conditional
        $evaluation->load([
            'customer',
            'collaterals',
            'externalLoans',
            'scores',
            'guarantors'
        ]);

        $customers = Customer::with(['user', 'evaluations.collaterals', 'evaluations.externalLoans'])->orderBy('id', 'desc')->get();
        // Get Asset Components (Neraca) - Removed as we now use direct columns

        // Get Scoring Components organized by Loan Scheme and then Category
        $scoringComponents = CreditScoringComponent::where('is_active', true)
            ->get()
            ->groupBy(['loan_scheme', 'category']);

        $aoUsers = User::role('AO')->get();
        $economicSectors = EconomicSector::all();
        $nonBankThirdParties = NonBankThirdParty::all();

        // Kabag sees the form in readonly mode
        $readonly = auth()->user()->can('approve evaluations') && !auth()->user()->hasRole('Admin');

        return view('evaluations.edit', compact(
            'evaluation',
            'customers',
            'scoringComponents',
            'aoUsers',
            'economicSectors',
            'nonBankThirdParties',
            'readonly'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        if (auth()->user()->cannot('update evaluations')) abort(403);
        // Validation
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'loan_term_months' => 'required|integer',
            'evaluation_date' => 'required|date',
            'loan_scheme' => 'required|string',
            'loan_type' => 'required|string',
            'customer_type' => 'required|string',
            'customer_status' => 'required|string',
            'customer_profile' => 'nullable|string',
            'customer_dependents' => 'nullable|integer',
            'office_branch' => 'required|string',
            'user_id' => 'required|exists:users,id',

            // Allow nulls for others as per store validation
            'loan_interest_rate' => 'nullable|numeric',
            'location_image' => 'nullable|string',
            'loan_purpose' => 'nullable|string',
            'seasonal_loan_repayment_source' => 'nullable|string',

            // Old Loan Details
            'old_loan_purpose' => 'nullable|string',
            'old_seasonal_loan_repayment_source' => 'nullable|string',
            'old_loan_type' => 'nullable|string',
            'old_loan_amount' => 'nullable|numeric',
            'old_loan_term_months' => 'nullable|integer',
            'old_loan_interest_rate' => 'nullable|numeric',

            // Entrepreneurship Details
            'customer_entreprenuership_legality' => 'nullable|string',
            'customer_entreprenuership_ownership' => 'nullable|string',
            'customer_entreprenuership_name' => 'nullable|string',
            'customer_entreprenuership_type' => 'nullable|string',
            'customer_entreprenuership_year' => 'nullable|integer',
            'customer_entreprenuership_tax_id' => 'nullable|string',
            'customer_entreprenuership_legality_id' => 'nullable|string',
            'customer_entreprenuership_legality_register_id' => 'nullable|string',
            'customer_entreprenuership_description' => 'nullable|string',
            'customer_entreprenuership_products' => 'nullable|string',
            'customer_entreprenuership_place_status' => 'nullable|string',
            'customer_entreprenuership_phone' => 'nullable|string',
            'customer_entreprenuership_employee_count' => 'nullable|string',

            // Company Details
            'customer_employee_status' => 'nullable|string',
            'customer_company_sector' => 'nullable|string',
            'customer_company_employee_count' => 'nullable|string',
            'customer_company_salary_frequency' => 'nullable|string',
            'customer_company_payday' => 'nullable|date',
            'customer_company_years' => 'nullable|string',

            // Custom Assets
            'custom_assets' => 'nullable|array',
            'custom_assets.*.name' => 'nullable|string',
            'custom_assets.*.type' => 'nullable|string',
            'custom_assets.*.estimated_price' => 'nullable|numeric',

            // New Neraca Fields
            'kas_usaha' => 'nullable|numeric',
            'piutang_usaha' => 'nullable|numeric',
            'persediaan' => 'nullable|numeric',
            'kewajiban_lancar' => 'nullable|numeric',
            'kewajiban_jangka_panjang' => 'nullable|numeric',
            'modal_usaha' => 'nullable|numeric',

            // Document Checklist
            'document_checklist' => 'nullable|json'
        ]);

        \Log::info('Update document_checklist incoming payload:', ['raw' => $request->document_checklist]);

        DB::beginTransaction();
        try {
            // Recalculate Totals (Copy of Store Logic)
            $cashInTotalBefore = ($request->cash_in_salary_before ?? 0) + ($request->cash_in_business_before ?? 0) + ($request->cash_in_other_before ?? 0);
            $cashInTotalAfter = ($request->cash_in_salary_after ?? 0) + ($request->cash_in_business_after ?? 0) + ($request->cash_in_other_after ?? 0) + ($request->capital_injection_amount ?? 0);

            $hhTotalBefore = ($request->hh_living_before ?? 0) + ($request->hh_utilities_before ?? 0) + ($request->hh_education_before ?? 0) + ($request->hh_telecom_before ?? 0) + ($request->hh_transport_before ?? 0) + ($request->hh_entertainment_before ?? 0) + ($request->hh_rent_before ?? 0) + ($request->hh_other_before ?? 0);
            $hhTotalAfter = ($request->hh_living_after ?? 0) + ($request->hh_utilities_after ?? 0) + ($request->hh_education_after ?? 0) + ($request->hh_telecom_after ?? 0) + ($request->hh_transport_after ?? 0) + ($request->hh_entertainment_after ?? 0) + ($request->hh_rent_after ?? 0) + ($request->hh_other_after ?? 0);

            $bizTotalBefore = ($request->biz_hpp_before ?? 0) + ($request->biz_labor_before ?? 0) + ($request->biz_telecom_before ?? 0) + ($request->biz_transport_before ?? 0) + ($request->biz_utilities_before ?? 0) + ($request->biz_rent_before ?? 0) + ($request->biz_other_before ?? 0);
            $bizTotalAfter = ($request->biz_hpp_after ?? 0) + ($request->biz_labor_after ?? 0) + ($request->biz_telecom_after ?? 0) + ($request->biz_transport_after ?? 0) + ($request->biz_utilities_after ?? 0) + ($request->biz_rent_after ?? 0) + ($request->biz_other_after ?? 0);

            // Calculate bank installments from external loans
            $totalExternalInstallment = 0;
            if ($request->has('external_loans')) {
                foreach ($request->external_loans as $loan) {
                    $totalExternalInstallment += (float) str_replace('.', '', $loan['installment_amount'] ?? '') ?: 0;
                }
            }
            $bankInstallmentsBefore = ($request->bank_bni_before ?? 0) + $totalExternalInstallment;
            $bankInstallmentsAfter = ($request->bank_bni_after ?? 0) + $totalExternalInstallment;

            $cashOutTotalBefore = $bankInstallmentsBefore + $hhTotalBefore + $bizTotalBefore + ($request->other_expenses_before ?? 0);
            $cashOutTotalAfter = $bankInstallmentsAfter + $hhTotalAfter + $bizTotalAfter + ($request->other_expenses_after ?? 0);

            $netCashFlowBefore = $cashInTotalBefore - $cashOutTotalBefore;
            $netCashFlowAfter = $cashInTotalAfter - $cashOutTotalAfter;

            $endOpBalanceBefore = ($request->op_opening_balance_before ?? 0) + $netCashFlowBefore;
            $endOpBalanceAfter = ($request->op_opening_balance_after ?? 0) + $netCashFlowAfter;

            // Costs
            $feeRate = ($request->loan_term_months < 3) ? 0.005 : 0.01;
            $loanProvisionCost = $request->loan_provision_cost ?? ($request->loan_amount * $feeRate);
            $loanAdminCost = $request->loan_administration_cost ?? ($request->loan_amount * $feeRate);
            $loanTotalRealizationCost = $loanProvisionCost + $loanAdminCost +
                ($request->loan_duty_stamp_cost ?? 0) +
                ($request->loan_notary_public_cost ?? 0) +
                ($request->loan_insurance_cost ?? 0) +
                ($request->loan_other_cost ?? 0);

            // Installment
            $interestRate = $request->loan_interest_rate ?? $request->interest_rate ?? 12;
            $tenor = $request->loan_term_months ?? $request->loan_tenor ?? 11;
            $monthlyInterest = round(($request->loan_amount * (($interestRate) / 100)) / 12);

            if ($request->loan_type === 'Pinjaman Musiman') {
                $monthlyPrincipal = 0;
            }
            else {
                $monthlyPrincipal = round($request->loan_amount / ($tenor > 0 ? $tenor : 1));
            }
            $monthlyInstallment = round($monthlyInterest + $monthlyPrincipal);

            // Balances
            $cashBankTotalBefore = $endOpBalanceBefore - $loanTotalRealizationCost + ($request->loan_amount ?? 0);
            $cashBankTotalAfter = $endOpBalanceAfter - $monthlyInstallment;
            $loanRemBalanceBefore = $request->loan_amount ?? 0;
            $loanRemBalanceAfter = $loanRemBalanceBefore - $monthlyPrincipal;

            $evaluationData = [
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
                'evaluation_date' => $request->evaluation_date,
                'loan_scheme' => $request->loan_scheme,
                'loan_type' => $request->loan_type,
                'loan_purpose' => $request->loan_purpose,
                'seasonal_loan_repayment_source' => $request->seasonal_loan_repayment_source,

                // Old Loan Details
                'old_loan_purpose' => $request->old_loan_purpose,
                'old_seasonal_loan_repayment_source' => $request->old_seasonal_loan_repayment_source,
                'old_loan_type' => $request->old_loan_type,
                'old_loan_amount' => $request->old_loan_amount,
                'old_loan_term_months' => $request->old_loan_term_months,
                'old_loan_interest_rate' => $request->old_loan_interest_rate,

                'customer_type' => $request->customer_type,
                'customer_status' => $request->customer_status,
                'loan_amount' => $request->loan_amount,
                'loan_term_months' => $tenor,
                'loan_interest_rate' => $interestRate,
                'customer_profile' => $request->customer_profile ?? '-',
                'customer_dependents' => $request->customer_dependents,
                'economic_sector' => $request->economic_sector ?? '-',
                'economic_sector_code' => $request->economic_sector_code ?? '-',
                'non_bank_third_party' => $request->non_bank_third_party ?? '-',
                'non_bank_third_party_code' => $request->non_bank_third_party_code ?? '-',
                'customer_employment_status' => $request->customer_employment_status,
                'customer_employee_status' => $request->customer_employee_status,
                'customer_company_sector' => $request->customer_company_sector,
                'customer_company_employee_count' => $request->customer_company_employee_count,
                'customer_company_salary_frequency' => $request->customer_company_salary_frequency,
                'customer_company_payday' => $request->customer_company_payday,
                'customer_company_name' => $request->customer_company_name,
                'customer_company_address' => $request->customer_company_address,
                'customer_company_phone' => $request->customer_company_phone,
                'customer_company_position' => $request->customer_company_position,
                'customer_entrepreneurship_status' => $request->customer_entrepreneurship_status,
                'customer_entreprenuership_type' => $request->customer_entreprenuership_type,
                'customer_entreprenuership_name' => $request->customer_entreprenuership_name,
                'customer_entreprenuership_legality' => $request->customer_entreprenuership_legality,
                'customer_entreprenuership_ownership' => $request->customer_entreprenuership_ownership,
                'customer_entreprenuership_year' => $request->customer_entreprenuership_year,
                'customer_entreprenuership_tax_id' => $request->customer_entreprenuership_tax_id,
                'customer_entreprenuership_legality_id' => $request->customer_entreprenuership_legality_id,
                'customer_entreprenuership_legality_register_id' => $request->customer_entreprenuership_legality_register_id,
                'customer_entreprenuership_description' => $request->customer_entreprenuership_description,
                'customer_entreprenuership_products' => $request->customer_entreprenuership_products,
                'customer_entreprenuership_place_status' => $request->customer_entreprenuership_place_status,
                'customer_entreprenuership_phone' => $request->customer_entreprenuership_phone,
                'customer_entreprenuership_employee_count' => $request->customer_entreprenuership_employee_count,
                'customer_company_years' => $request->customer_company_years,

                'business_latitude' => $request->business_latitude,
                'business_longitude' => $request->business_longitude,
                'business_village' => $request->business_village,
                'business_district' => $request->business_district,
                'business_regency' => $request->business_regency,
                'business_province' => $request->business_province,

                'min_monthly_cash_bank_balance' => $request->min_monthly_cash_bank_balance ?? 0,
                'op_opening_balance_before' => $request->op_opening_balance_before ?? 0,
                'op_opening_balance_after' => $request->op_opening_balance_after ?? 0,
                'cash_in_salary_before' => $request->cash_in_salary_before ?? 0,
                'cash_in_salary_after' => $request->cash_in_salary_after ?? 0,
                'cash_in_business_before' => $request->cash_in_business_before ?? 0,
                'cash_in_business_after' => $request->cash_in_business_after ?? 0,
                'cash_in_other_before' => $request->cash_in_other_before ?? 0,
                'cash_in_other_after' => $request->cash_in_other_after ?? 0,
                'cash_in_other_details' => $request->has('other_incomes') ? json_encode($request->other_incomes) : null,
                'capital_injection_amount' => $request->capital_injection_amount ?? 0,
                'loan_duty_stamp_cost' => $request->loan_duty_stamp_cost ?? 0,

                'cash_in_total_before' => $cashInTotalBefore,
                'cash_in_total_after' => $cashInTotalAfter,

                'other_bank_installments_before' => $bankInstallmentsBefore,
                'other_bank_installments_after' => $bankInstallmentsAfter,

                'hh_living_before' => $request->hh_living_before ?? 0,
                'hh_living_after' => $request->hh_living_after ?? 0,
                'hh_utilities_before' => $request->hh_utilities_before ?? 0,
                'hh_utilities_after' => $request->hh_utilities_after ?? 0,
                'hh_education_before' => $request->hh_education_before ?? 0,
                'hh_education_after' => $request->hh_education_after ?? 0,
                'hh_telecom_before' => $request->hh_telecom_before ?? 0,
                'hh_telecom_after' => $request->hh_telecom_after ?? 0,
                'hh_transport_before' => $request->hh_transport_before ?? 0,
                'hh_transport_after' => $request->hh_transport_after ?? 0,
                'hh_entertainment_before' => $request->hh_entertainment_before ?? 0,
                'hh_entertainment_after' => $request->hh_entertainment_after ?? 0,
                'hh_rent_before' => $request->hh_rent_before ?? 0,
                'hh_rent_after' => $request->hh_rent_after ?? 0,
                'hh_other_before' => $request->hh_other_before ?? 0,
                'hh_other_after' => $request->hh_other_after ?? 0,

                'biz_hpp_before' => $request->biz_hpp_before ?? 0,
                'biz_hpp_after' => $request->biz_hpp_after ?? 0,
                'biz_labor_before' => $request->biz_labor_before ?? 0,
                'biz_labor_after' => $request->biz_labor_after ?? 0,
                'biz_telecom_before' => $request->biz_telecom_before ?? 0,
                'biz_telecom_after' => $request->biz_telecom_after ?? 0,
                'biz_transport_before' => $request->biz_transport_before ?? 0,
                'biz_transport_after' => $request->biz_transport_after ?? 0,
                'biz_utilities_before' => $request->biz_utilities_before ?? 0,
                'biz_utilities_after' => $request->biz_utilities_after ?? 0,
                'biz_rent_before' => $request->biz_rent_before ?? 0,
                'biz_rent_after' => $request->biz_rent_after ?? 0,
                'biz_other_before' => $request->biz_other_before ?? 0,
                'biz_other_after' => $request->biz_other_after ?? 0,

                'other_expenses_before' => $request->other_expenses_before ?? 0,
                'other_expenses_after' => $request->other_expenses_after ?? 0,

                'hh_total_before' => $hhTotalBefore,
                'hh_total_after' => $hhTotalAfter,
                'biz_total_before' => $bizTotalBefore,
                'biz_total_after' => $bizTotalAfter,
                'cash_out_total_before' => $cashOutTotalBefore,
                'cash_out_total_after' => $cashOutTotalAfter,
                'net_cash_flow_before' => $netCashFlowBefore,
                'net_cash_flow_after' => $netCashFlowAfter,
                'end_op_balance_before' => $endOpBalanceBefore,
                'end_op_balance_after' => $endOpBalanceAfter,

                'loan_provision_cost' => $loanProvisionCost,
                'loan_administration_cost' => $loanAdminCost,
                'loan_provision_rate' => $request->loan_provision_rate,
                'loan_admin_rate' => $request->loan_admin_rate,
                'loan_notary_public_cost' => $request->loan_notary_public_cost ?? 0,
                'loan_insurance_cost' => $request->loan_insurance_cost ?? 0,
                'loan_other_cost' => $request->loan_other_cost ?? 0,
                'loan_total_cost' => $loanTotalRealizationCost,

                // Rekomendasi Costs
                'rekomendasi_loan_provision_cost' => $request->rekomendasi_loan_provision_cost ?? 0,
                'rekomendasi_loan_administration_cost' => $request->rekomendasi_loan_administration_cost ?? 0,
                'rekomendasi_loan_duty_stamp_cost' => $request->rekomendasi_loan_duty_stamp_cost ?? 0,
                'rekomendasi_loan_notary_public_cost' => $request->rekomendasi_loan_notary_public_cost ?? 0,
                'rekomendasi_loan_insurance_cost' => $request->rekomendasi_loan_insurance_cost ?? 0,
                'rekomendasi_loan_other_cost' => $request->rekomendasi_loan_other_cost ?? 0,

                // RPC Ratio
                'rpc_ratio' => $request->rpc_ratio,

                'installment_proposed_rate' => $interestRate,
                'installment_proposed_term' => $tenor,
                'installment_proposed_total' => $monthlyInstallment,
                'installment_proposed_interest' => $monthlyInterest,
                'installment_proposed_principal' => $monthlyPrincipal,

                'cash_bank_total_before' => $cashBankTotalBefore,
                'cash_bank_total_after' => $cashBankTotalAfter,
                'loan_rem_balance_before' => $loanRemBalanceBefore,
                'loan_rem_balance_after' => $loanRemBalanceAfter,

                'roi_percent' => $request->roi_percent ?? 0,
                'roe_percent' => $request->roe_percent ?? 0,
                'dscr' => $request->dscr ?? 0,
                'debt_to_income_ratio' => $request->dti_percent ?? 0,
                'net_profit_margin' => $request->pm_percent ?? 0,
                'installment_proposed_amount' => $request->loan_installment ?? 0,

                // Neraca - Aktiva (Assets)
                'gold_before' => $request->gold_before ?? 0,
                'gold_after' => $request->gold_after ?? 0,
                'receivables_before' => $request->receivables_before ?? 0,
                'receivables_after' => $request->receivables_after ?? 0,
                'other_assets_before' => $request->other_assets_before ?? 0,
                'other_assets_after' => $request->other_assets_after ?? 0,

                // Neraca - Pasiva (Liabilities)
                'liab_third_party_before' => $request->liab_third_party_before ?? 0,
                'liab_third_party_after' => $request->liab_third_party_after ?? 0,
                'liab_bpr_before' => $request->liab_bpr_before ?? 0,
                'liab_bpr_after' => $request->liab_bpr_after ?? 0,
                'liab_other_before' => $request->liab_other_before ?? 0,
                'liab_other_after' => $request->liab_other_after ?? 0,

                // Neraca - Modal (Equity & Profit)
                'equity_own_before' => $request->equity_own_before ?? 0,
                'equity_own_after' => $request->equity_own_after ?? 0,
                'profit_current_before' => $request->profit_current_before ?? 0,
                'profit_current_after' => $request->profit_current_after ?? 0,
                'profit_past_before' => $request->profit_past_before ?? 0,
                'profit_past_after' => $request->profit_past_after ?? 0,

                // New Neraca Fields
                'kas_usaha' => $request->kas_usaha ?? 0,
                'piutang_usaha' => $request->piutang_usaha ?? 0,
                'persediaan' => $request->persediaan ?? 0,
                'kewajiban_lancar' => $request->kewajiban_lancar ?? 0,
                'kewajiban_jangka_panjang' => $request->kewajiban_jangka_panjang ?? 0,
                'modal_usaha' => $request->modal_usaha ?? 0,

                // Character Scoring (Part 6)
                'char_credit_bureau' => $request->char_credit_bureau ?? 0,
                'char_info_consistency' => $request->char_info_consistency ?? 0,
                'char_relationship' => $request->char_relationship ?? 0,
                'char_stability' => $request->char_stability ?? 0,
                'char_reputation' => $request->char_reputation ?? 0,
                'char_total_score' => $request->char_total_score ?? 0,

                // Capacity Scoring (Part 6 continued)
                'cap_rpc' => $request->cap_rpc ?? 0,
                'cap_lama_usaha' => $request->cap_lama_usaha ?? 0,
                'cap_usia' => $request->cap_usia ?? 0,
                'cap_pengelolaan' => $request->cap_pengelolaan ?? 0,
                'cap_total_score' => $request->cap_total_score ?? 0,

                // Capital Scoring
                'capital_dar' => $request->capital_dar ?? 0,
                'capital_der' => $request->capital_der ?? 0,
                'capital_total_score' => $request->capital_total_score ?? 0,

                // Condition Scoring
                'cond_lokasi' => $request->cond_lokasi ?? 0,
                'cond_profit' => $request->cond_profit ?? 0,
                'cond_dscr' => $request->cond_dscr ?? 0,
                'condition_total_score' => $request->condition_total_score ?? 0,

                // Collateral Scoring
                'col_kepemilikan' => $request->col_kepemilikan ?? 0,
                'col_peruntukan' => $request->col_peruntukan ?? 0,
                'col_lebar_jalan' => $request->col_lebar_jalan ?? 0,
                'col_coverage' => $request->col_coverage ?? 0,
                'col_marketable' => $request->col_marketable ?? 0,
                'col_total_score' => $request->col_total_score ?? 0,

                // Document Checklist
                'document_checklist' => $request->has('document_checklist') ? json_decode($request->document_checklist, true) : null,
            ];

            // Compute Final Score centrally via Service
            $creditScoringService = new CreditScoringService();
            $scoringResult = $creditScoringService->calculateScores($evaluationData);
            $evaluationData['final_score'] = $scoringResult['final_score'];

            // Handle Photo Uploads
            $photoFields = ['business_legality_photo', 'business_detail_1_photo', 'business_detail_2_photo'];
            foreach ($photoFields as $inputName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = time() . '_' . $inputName . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('evaluations/photos', $filename, 'local');
                    $colName = str_replace('_photo', '_path', $inputName);
                    $evaluationData[$colName] = $filename;
                }
            }

            // Handle Map Image
            Log::info('Checking location_image', ['has_image' => !empty($request->location_image), 'length' => strlen($request->location_image ?? '')]);
            if (!empty($request->location_image) && strlen($request->location_image) > 100) {
                $image = $request->location_image;
                $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'map_' . time() . '_' . uniqid() . '.png';
                $locationImagePath = 'evaluations/map/' . $imageName;

                $saved = \Illuminate\Support\Facades\Storage::disk('local')->put($locationImagePath, base64_decode($image));
                Log::info('Map Image Save Attempt', ['path' => $locationImagePath, 'success' => $saved]);

                $evaluationData['business_location_image_path'] = $imageName;
            }
            else {
                Log::info('Map Image Logic Skipped', ['reason' => 'Empty or too short']);
            }

            // Calculate Path Distance
            if (!empty($evaluationData['business_latitude']) && !empty($evaluationData['business_longitude'])) {
                $evaluationData['path_distance'] = $this->calculateDistance(
                    $evaluationData['business_latitude'],
                    $evaluationData['business_longitude'],
                    -7.487391381663846,
                    112.44006721604295
                );
            }

            \Log::info('EvaluationData immediately before update', ['document_checklist' => $evaluationData['document_checklist'] ?? 'MISSING_KEY']);

            $evaluation->update($evaluationData);

            // Update Assets (Removed, now using direct columns)

            // Sync Custom Assets (Delete & Recreate)
            $evaluation->customAssets()->delete();
            if ($request->has('custom_assets')) {
                foreach ($request->custom_assets as $customAsset) {
                    if (empty($customAsset['name']) && empty($customAsset['type'])) {
                        continue;
                    }
                    $evaluation->customAssets()->create([
                        'name' => $customAsset['name'] ?? '-',
                        'type' => $customAsset['type'] ?? '-',
                        'estimated_price' => str_replace('.', '', $customAsset['estimated_price'] ?? '') ?: 0,
                    ]);
                }
            }

            // Sync Guarantors (Delete & Recreate)
            $evaluation->guarantors()->delete();
            if ($request->has('guarantors')) {
                foreach ($request->guarantors as $guarantor) {
                    if (empty($guarantor['name'])) {
                        continue;
                    }
                    $evaluation->guarantors()->create([
                        'name' => $guarantor['name'],
                        'relationship' => $guarantor['relationship'] ?? '-',
                    ]);
                }
            }

            // Sync External Loans (Delete & Recreate)
            $evaluation->externalLoans()->delete();
            if ($request->has('external_loans')) {
                foreach ($request->external_loans as $loan) {
                    $loan['original_amount'] = str_replace('.', '', $loan['original_amount'] ?? '') ?: 0;
                    $loan['outstanding_balance'] = str_replace('.', '', $loan['outstanding_balance'] ?? '') ?: 0;
                    $loan['installment_amount'] = str_replace('.', '', $loan['installment_amount'] ?? '') ?: 0;
                    $evaluation->externalLoans()->create($loan);
                }
            }

            // Sync Collaterals (Delete & Recreate with Image Preservation)
            $evaluation->collaterals()->delete();
            if ($request->has('collaterals')) {
                foreach ($request->collaterals as $index => $collateralData) {
                    $dbData = [
                        'type' => $collateralData['type'] ?? 'unknown',
                        'owner_name' => $collateralData['owner_name'] ?? '-',
                        'owner_ktp' => $collateralData['owner_ktp'] ?? null,
                        'proof_type' => $collateralData['proof_type'] ?? '-',
                        'proof_number' => $collateralData['proof_number'] ?? '-',
                        'market_value' => str_replace('.', '', $collateralData['market_value'] ?? '') ?: 0,
                        'bank_value' => str_replace('.', '', $collateralData['bank_value'] ?? '') ?: 0,
                        'location_address' => $collateralData['location_address'] ?? null,
                        'latitude' => $collateralData['latitude'] ?? null,
                        'longitude' => $collateralData['longitude'] ?? null,
                        'village' => $collateralData['village'] ?? null,
                        'district' => $collateralData['district'] ?? null,
                        'regency' => $collateralData['regency'] ?? null,
                        'province' => $collateralData['province'] ?? null,
                    ];

                    // Calculate path distance for this collateral
                    if (!empty($dbData['latitude']) && !empty($dbData['longitude'])) {
                        $dbData['path_distance'] = $this->calculateDistance(
                            $dbData['latitude'],
                            $dbData['longitude'],
                            -7.487391381663846,
                            112.44006721604295
                        );
                    }

                    if ($dbData['type'] === 'vehicle') {
                        $dbData['vehicle_brand'] = $collateralData['brand'] ?? '';
                        $dbData['vehicle_model'] = $collateralData['model'] ?? '';
                        $dbData['vehicle_year'] = $collateralData['year'] ?? null;
                        $dbData['vehicle_color'] = $collateralData['color'] ?? null;
                        $dbData['vehicle_plate_number'] = $collateralData['police_number'] ?? null;
                        $dbData['vehicle_frame_number'] = $collateralData['chassis_number'] ?? null;
                        $dbData['vehicle_engine_number'] = $collateralData['engine_number'] ?? null;
                    }
                    elseif ($dbData['type'] === 'certificate') {
                        $dbData['property_surface_area'] = $collateralData['land_area'] ?? 0;
                        $dbData['property_building_area'] = $collateralData['building_area'] ?? 0;
                        $dbData['property_address'] = $collateralData['location_address'] ?? null;

                        $dbData['peruntukan_tanah'] = $collateralData['peruntukan_tanah'] ?? null;
                        $dbData['lebar_jalan'] = $collateralData['lebar_jalan'] ?? null;
                        $dbData['kondisi_bangunan'] = $collateralData['kondisi_bangunan'] ?? null;
                        $dbData['material_pondasi'] = $collateralData['material_pondasi'] ?? null;
                        $dbData['material_tembok'] = $collateralData['material_tembok'] ?? null;
                        $dbData['material_atap'] = $collateralData['material_atap'] ?? null;
                        $dbData['material_kusen'] = $collateralData['material_kusen'] ?? null;
                        $dbData['material_daun_pintu'] = $collateralData['material_daun_pintu'] ?? null;
                    }

                    // Handle Images (New Upload OR Existing)
                    $maxImages = 4; // Max 4 slots
                    for ($i = 0; $i < $maxImages; $i++) {
                        $colName = ($dbData['type'] === 'certificate') ? 'property_image_' . ($i + 1) : 'vehicle_image_' . ($i + 1);

                        // Check for new upload
                        if ($request->hasFile("collaterals.$index.images.$i")) {
                            $imageFile = $request->file("collaterals.$index.images.$i");
                            $filename = 'col_' . time() . '_' . $index . '_' . $i . '.' . $imageFile->getClientOriginalExtension();
                            $imageFile->storeAs('evaluations/collaterals', $filename, 'local');
                            $dbData[$colName] = $filename;
                            if ($i == ($dbData['type'] === 'certificate' ? 3 : 0))
                                $dbData['image_proof'] = $filename;
                        }
                        // Check for existing
                        elseif (isset($collateralData['existing_images'][$i]) && !empty($collateralData['existing_images'][$i])) {
                            $filename = $collateralData['existing_images'][$i];
                            $dbData[$colName] = $filename;
                            if ($i == ($dbData['type'] === 'certificate' ? 3 : 0))
                                $dbData['image_proof'] = $filename;
                        }
                    }

                    $evaluation->collaterals()->create($dbData);
                }
            }

            // Sync Scores
            $evaluation->scores()->delete();
            if ($request->has('scores')) {
                foreach ($request->scores as $componentId => $scoreData) {
                    $score = $scoreData['score'] ?? 0;
                    $weight = CreditScoringComponent::find($componentId)->weight ?? 0;
                    $weightedValue = ($score * $weight) / 100;
                    $evaluation->scores()->create([
                        'credit_scoring_component_id' => $componentId,
                        'score' => $score,
                        'weight' => $weight,
                        'weighted_value' => $weightedValue,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil diperbarui.');

        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Evaluation Update Error: ' . $e->getMessage());
            return back()->with('error', 'Error updating evaluation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        if (auth()->user()->cannot('delete evaluations')) abort(403);
        $evaluation->delete();

        return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil dihapus.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        if (auth()->user()->cannot('delete evaluations')) {
            abort(403, 'Unauthorized action.');
        }

        $evaluation = Evaluation::onlyTrashed()->findOrFail($id);
        $evaluation->restore();

        return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil dipulihkan.');
    }
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in km

        return $distance;
    }

    /**
     * Send evaluation for kabag approval (draft -> pending)
     */
    public function sendForApproval(Evaluation $evaluation)
    {
        if (auth()->user()->cannot('create evaluations')) abort(403);
        if ($evaluation->approval_status !== 'draft') {
            return redirect()->route('evaluations.index')->with('error', 'Evaluasi tidak dapat dikirim karena statusnya bukan draft.');
        }

        $evaluation->approval_status = 'pending';
        $evaluation->save();

        // Create marquee notification
        $userName = auth()->user()->name;
        $customerName = $evaluation->customer->name ?? 'N/A';
        EvaluationNotification::create([
            'message' => "AO {$userName} baru saja mengirim persetujuan evaluasi an. {$customerName}",
        ]);

        return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil dikirim untuk persetujuan.');
    }

    /**
     * Revoke evaluation approval/rejection or pending back to draft
     */
    public function revokeApproval(Evaluation $evaluation)
    {
        if (auth()->user()->cannot('update evaluations')) abort(403);

        // Users with approve permission can revoke approved/rejected within 24 hours
        if (in_array($evaluation->approval_status, ['approved', 'rejected']) && auth()->user()->can('approve evaluations')) {
            $approvalTime = $evaluation->updated_at;
            $hoursSinceApproval = now()->diffInHours($approvalTime);

            if ($hoursSinceApproval > 24) {
                return redirect()->route('evaluations.edit', $evaluation->id)
                    ->with('error', 'Tidak dapat membatalkan keputusan. Batas waktu 24 jam telah terlewati.');
            }

            $evaluation->approval_status = 'pending';
            $evaluation->approval_note = null;
            $evaluation->approved_amount = null;
            $evaluation->approved_tenor = null;
            $evaluation->approved_interest_rate = null;
            $evaluation->approval_user_id = null; // Clear approval user ID
            $evaluation->save();

            return redirect()->route('evaluations.edit', $evaluation->id)
                ->with('success', 'Keputusan berhasil dibatalkan. Evaluasi kembali ke status pending.');
        }

        // AO can revoke pending back to draft
        if ($evaluation->approval_status !== 'pending') {
            return redirect()->route('evaluations.index')->with('error', 'Evaluasi tidak dapat ditarik kembali.');
        }

        $evaluation->approval_status = 'draft';
        $evaluation->save();

        return redirect()->route('evaluations.index')->with('success', 'Evaluasi berhasil ditarik kembali ke draft.');
    }

    /**
     * Process kabag approval or rejection
     */
    public function processApproval(Request $request, Evaluation $evaluation)
    {
        if (auth()->user()->cannot('approve evaluations')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'approval_status' => 'required|in:approved,rejected',
            'approval_note' => 'nullable|string',
            'approved_amount' => 'nullable|string',
            'approved_tenor' => 'nullable|numeric|min:1',
            'approved_interest_rate' => 'nullable|numeric|min:0',
        ]);

        $evaluation->approval_status = $request->input('approval_status');
        $evaluation->approval_note = $request->input('approval_note');

        if ($request->input('approval_status') === 'approved') {
            $approvedAmount = $request->input('approved_amount');
            if ($approvedAmount) {
                $approvedAmount = str_replace('.', '', $approvedAmount);
                $evaluation->approved_amount = $approvedAmount ?: null;
            }
            
            $evaluation->approved_tenor = $request->input('approved_tenor');
            $evaluation->approved_interest_rate = $request->input('approved_interest_rate');
        }

        $evaluation->approval_user_id = auth()->id(); // Set the user who approved/rejected
        $evaluation->save();

        // Create marquee notification
        $userName = auth()->user()->name;
        $customerName = $evaluation->customer->name ?? 'N/A';
        $action = $evaluation->approval_status === 'approved' ? 'menyetujui' : 'menolak';
        EvaluationNotification::create([
            'message' => "{$userName} baru saja {$action} evaluasi an. {$customerName}",
        ]);

        $message = $evaluation->approval_status === 'approved' ? 'Evaluasi berhasil disetujui.' : 'Evaluasi telah ditolak.';
        return redirect()->route('evaluations.index')->with('success', $message);
    }
}
