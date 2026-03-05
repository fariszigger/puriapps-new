<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Account Officer
            $table->string('application_id')->nullable()->unique();
            $table->string('office_branch')->default('Kantor Pusat');
            $table->date('evaluation_date');
            
            // Loan Parameters
            $table->string('loan_type'); // Pinjaman Angsuran / Musiman
            $table->decimal('loan_amount', 15, 2);
            $table->integer('loan_term_months');
            $table->decimal('loan_interest_rate', 5, 2)->nullable(); // percent
            $table->string('loan_scheme')->nullable(); // Skema 1, Skema 2, etc. (often mapped to scoring)
            $table->text('loan_purpose')->nullable();
            
            // For Seasonal Loans
            $table->string('seasonal_loan_repayment_source')->nullable();
            
            // Old Loan Info (if any)
            $table->string('old_loan_type')->nullable();
            $table->decimal('old_loan_amount', 15, 2)->nullable();
            $table->integer('old_loan_term_months')->nullable();
            $table->decimal('old_loan_interest_rate', 5, 2)->nullable();
            $table->text('old_loan_purpose')->nullable();
            $table->string('old_seasonal_loan_repayment_source')->nullable();

            // Customer Info Snapshot & Form Data
            $table->string('customer_type')->default('Lama'); // Lama / Baru
            $table->string('customer_status')->default('Wirausaha'); // Wirausaha / Karyawan / Profesional
            
            // Employment / Business Details
            $table->string('economic_sector')->nullable();
            $table->string('economic_sector_code')->nullable();
            $table->string('non_bank_third_party')->nullable();
            $table->string('non_bank_third_party_code')->nullable();

            $table->string('customer_entrepreneurship_status')->nullable(); 
            $table->string('customer_employment_status')->nullable(); // Untuk Karyawan
            $table->text('customer_profile')->nullable();
            
            // Business Specific
            $table->string('customer_entreprenuership_legality')->nullable(); // Ada / Tidak Ada
            $table->string('customer_entreprenuership_ownership')->nullable();
            $table->string('customer_entreprenuership_name')->nullable();
            $table->string('customer_entreprenuership_type')->nullable(); // Bidang Usaha
            $table->integer('customer_entreprenuership_year')->nullable(); // Lama Usaha (Tahun)
            $table->string('customer_entreprenuership_tax_id')->nullable(); // NPWP
            $table->string('customer_entreprenuership_legality_id')->nullable(); // NIB
            $table->string('customer_entreprenuership_legality_register_id')->nullable(); // SKU
            
            // Employee / Professional Specific
            $table->string('customer_company_name')->nullable();
            $table->text('customer_company_address')->nullable();
            $table->string('customer_company_position')->nullable();
            $table->string('customer_company_years')->nullable();
            $table->string('customer_company_phone')->nullable();
            
            // Images
            $table->string('business_legality_path')->nullable();
            $table->string('business_detail_1_path')->nullable();
            $table->string('business_detail_2_path')->nullable();
            $table->string('business_location_image_path')->nullable();
            $table->decimal('path_distance', 8, 2)->nullable();

            // Location
            $table->string('business_latitude')->nullable();
            $table->string('business_longitude')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_regency')->nullable();
            $table->string('business_district')->nullable();
            $table->string('business_village')->nullable();
            
            // --- NERACA FIELDS ---
            // Neraca - Aktiva (Assets)
            $table->decimal('gold_before', 15, 2)->default(0);
            $table->decimal('gold_after', 15, 2)->default(0);
            $table->decimal('receivables_before', 15, 2)->default(0);
            $table->decimal('receivables_after', 15, 2)->default(0);
            $table->decimal('other_assets_before', 15, 2)->default(0);
            $table->decimal('other_assets_after', 15, 2)->default(0);

            // Neraca - Pasiva (Liabilities)
            $table->decimal('liab_third_party_before', 15, 2)->default(0);
            $table->decimal('liab_third_party_after', 15, 2)->default(0);
            $table->decimal('liab_bpr_before', 15, 2)->default(0);
            $table->decimal('liab_bpr_after', 15, 2)->default(0);
            $table->decimal('liab_other_before', 15, 2)->default(0);
            $table->decimal('liab_other_after', 15, 2)->default(0);

            // Neraca - Modal (Equity & Profit)
            $table->decimal('equity_own_before', 15, 2)->default(0);
            $table->decimal('equity_own_after', 15, 2)->default(0);
            $table->decimal('profit_current_before', 15, 2)->default(0);
            $table->decimal('profit_current_after', 15, 2)->default(0);
            $table->decimal('profit_past_before', 15, 2)->default(0);
            $table->decimal('profit_past_after', 15, 2)->default(0);
            // ------------------

            // Cash Flow Fields
            $table->decimal('opening_cash_balance', 15, 2)->default(0);
            $table->decimal('opening_savings_balance', 15, 2)->default(0);
            $table->decimal('opening_giro_balance', 15, 2)->default(0);
            $table->decimal('min_monthly_cash_bank_balance', 15, 2)->default(0);
            
            $table->decimal('op_opening_balance_before', 15, 2)->default(0);
            $table->decimal('op_opening_balance_after', 15, 2)->default(0);
            
            $table->decimal('cash_in_salary_before', 15, 2)->default(0);
            $table->decimal('cash_in_salary_after', 15, 2)->default(0);
            $table->decimal('cash_in_business_before', 15, 2)->default(0);
            $table->decimal('cash_in_business_after', 15, 2)->default(0);
            $table->decimal('cash_in_other_before', 15, 2)->default(0);
            $table->decimal('cash_in_other_after', 15, 2)->default(0);
            $table->json('cash_in_other_details')->nullable();
            
            $table->decimal('cash_in_total_before', 15, 2)->default(0);
            $table->decimal('cash_in_total_after', 15, 2)->default(0);

            // Expenses - Other Bank Installments
            $table->decimal('other_bank_installments_before', 15, 2)->default(0);
            $table->decimal('other_bank_installments_after', 15, 2)->default(0);

            // Expenses - Household
            $table->decimal('hh_living_before', 15, 2)->default(0); $table->decimal('hh_living_after', 15, 2)->default(0);
            $table->decimal('hh_utilities_before', 15, 2)->default(0); $table->decimal('hh_utilities_after', 15, 2)->default(0);
            $table->decimal('hh_education_before', 15, 2)->default(0); $table->decimal('hh_education_after', 15, 2)->default(0);
            $table->decimal('hh_telecom_before', 15, 2)->default(0); $table->decimal('hh_telecom_after', 15, 2)->default(0);
            $table->decimal('hh_transport_before', 15, 2)->default(0); $table->decimal('hh_transport_after', 15, 2)->default(0);
            $table->decimal('hh_entertainment_before', 15, 2)->default(0); $table->decimal('hh_entertainment_after', 15, 2)->default(0);
            $table->decimal('hh_rent_before', 15, 2)->default(0); $table->decimal('hh_rent_after', 15, 2)->default(0);
            $table->decimal('hh_other_before', 15, 2)->default(0); $table->decimal('hh_other_after', 15, 2)->default(0);
            $table->decimal('hh_total_before', 15, 2)->default(0); $table->decimal('hh_total_after', 15, 2)->default(0);

            // Expenses - Business
            $table->decimal('biz_hpp_before', 15, 2)->default(0); $table->decimal('biz_hpp_after', 15, 2)->default(0);
            $table->decimal('biz_labor_before', 15, 2)->default(0); $table->decimal('biz_labor_after', 15, 2)->default(0);
            $table->decimal('biz_telecom_before', 15, 2)->default(0); $table->decimal('biz_telecom_after', 15, 2)->default(0);
            $table->decimal('biz_transport_before', 15, 2)->default(0); $table->decimal('biz_transport_after', 15, 2)->default(0);
            $table->decimal('biz_utilities_before', 15, 2)->default(0); $table->decimal('biz_utilities_after', 15, 2)->default(0);
            $table->decimal('biz_rent_before', 15, 2)->default(0); $table->decimal('biz_rent_after', 15, 2)->default(0);
            $table->decimal('biz_other_before', 15, 2)->default(0); $table->decimal('biz_other_after', 15, 2)->default(0);
            $table->decimal('biz_total_before', 15, 2)->default(0); $table->decimal('biz_total_after', 15, 2)->default(0);

            // Expenses - Other
            $table->decimal('other_expenses_before', 15, 2)->default(0); $table->decimal('other_expenses_after', 15, 2)->default(0);
            
            // Cash Out Total
            $table->decimal('cash_out_total_before', 15, 2)->default(0);
            $table->decimal('cash_out_total_after', 15, 2)->default(0);
            
            // Net Cash Flow
            $table->decimal('net_cash_flow_before', 15, 2)->default(0);
            $table->decimal('net_cash_flow_after', 15, 2)->default(0);
            
            // End Operational Balance
            $table->decimal('end_op_balance_before', 15, 2)->default(0);
            $table->decimal('end_op_balance_after', 15, 2)->default(0);
            
            // Capital Injection
            $table->decimal('capital_injection_amount', 15, 2)->default(0);

            // --- Realisasi Kredit & Biaya ---
            // Loan Realization Costs
            $table->decimal('loan_provision_cost', 15, 2)->default(0);
            $table->decimal('loan_administration_cost', 15, 2)->default(0);
            $table->decimal('loan_duty_stamp_cost', 15, 2)->default(0);
            $table->decimal('loan_notary_public_cost', 15, 2)->default(0);
            $table->decimal('loan_insurance_cost', 15, 2)->default(0);
            $table->decimal('loan_other_cost', 15, 2)->default(0);
            
            $table->decimal('loan_provision_rate', 5, 2)->nullable();
            $table->decimal('loan_admin_rate', 5, 2)->nullable();

            $table->decimal('loan_total_cost', 15, 2)->default(0);

            // Rekomendasi Costs
            $table->decimal('rekomendasi_loan_provision_cost', 15, 2)->default(0);
            $table->decimal('rekomendasi_loan_administration_cost', 15, 2)->default(0);
            $table->decimal('rekomendasi_loan_duty_stamp_cost', 15, 2)->default(0);
            $table->decimal('rekomendasi_loan_notary_public_cost', 15, 2)->default(0);
            $table->decimal('rekomendasi_loan_insurance_cost', 15, 2)->default(0);
            $table->decimal('rekomendasi_loan_other_cost', 15, 2)->default(0);

            // Proposed Installment details
            $table->decimal('installment_proposed_rate', 5, 2)->nullable();
            $table->integer('installment_proposed_term')->nullable();
            $table->decimal('installment_proposed_total', 15, 2)->default(0);
            $table->decimal('installment_proposed_interest', 15, 2)->default(0);
            $table->decimal('installment_proposed_principal', 15, 2)->default(0);
            $table->decimal('installment_proposed_amount', 15, 2)->default(0); // If manually overridden
            
            // Final Balances
            $table->decimal('cash_bank_total_before', 15, 2)->default(0);
            $table->decimal('cash_bank_total_after', 15, 2)->default(0);
            $table->decimal('loan_rem_balance_before', 15, 2)->default(0);
            $table->decimal('loan_rem_balance_after', 15, 2)->default(0);

            // Financial Ratios
            $table->decimal('roi_percent', 8, 2)->default(0);
            $table->decimal('roe_percent', 8, 2)->default(0);
            $table->decimal('dscr', 8, 2)->default(0);
            $table->decimal('debt_to_income_ratio', 8, 2)->default(0);
            $table->decimal('net_profit_margin', 8, 2)->default(0);
            $table->decimal('rpc_ratio', 5, 2)->nullable();
            
            // --- 5C Scoring ---
            // Character Scoring
            $table->integer('char_credit_bureau')->default(0);
            $table->integer('char_info_consistency')->default(0);
            $table->integer('char_relationship')->default(0);
            $table->integer('char_stability')->default(0);
            $table->integer('char_reputation')->default(0);
            $table->decimal('char_total_score', 5, 2)->default(0);
            
            // Capacity Scoring
            $table->integer('cap_rpc')->default(0);
            $table->integer('cap_lama_usaha')->default(0);
            $table->integer('cap_usia')->default(0);
            $table->integer('cap_pengelolaan')->default(0);
            $table->decimal('cap_total_score', 5, 2)->default(0);

            // Final Status
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'revision_required'])->default('draft');
            $table->text('status_note')->nullable();
            
            // Approval Status
            $table->string('approval_status')->default('draft');
            $table->longText('approval_note')->nullable();
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->integer('approved_tenor')->nullable();
            $table->decimal('approved_interest_rate', 5, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
