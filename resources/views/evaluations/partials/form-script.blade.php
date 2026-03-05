<script>
    console.log('Form Script loaded');
    document.addEventListener('alpine:init', () => {
        Alpine.data('evaluationForm', () => ({
            currentStep: 1,
            paymentSchedule: [],
            entrepreneurshipStatus: {{ Js::from(old('customer_entrepreneurship_status', isset($evaluation) ? $evaluation->customer_entrepreneurship_status : '')) }},
            employmentStatus: {{ Js::from(old('customer_employment_status', isset($evaluation) ? $evaluation->customer_employment_status : '')) }},
            salaryFrequency: {{ Js::from(old('customer_company_salary_frequency', isset($evaluation) ? $evaluation->customer_company_salary_frequency : '')) }},
            legality: {{ Js::from(old('customer_entreprenuership_legality', isset($evaluation) ? $evaluation->customer_entreprenuership_legality : '')) }},
            ownership: {{ Js::from(old('customer_entreprenuership_ownership', isset($evaluation) ? $evaluation->customer_entreprenuership_ownership : '')) }},

            formattedCustomers: {{ Js::from($customers) }},
            search: '',
            showModal: {{ isset($evaluation) ? 'false' : 'true' }},
            selectedCustomer: {{ Js::from(isset($evaluation) ? $evaluation->customer : null) }},


            init() {
                console.log('Alpine Form Initialized');
            },
            currentPage: 1,
            itemsPerPage: 25,

            // Sync Configuration
            syncFields: [
                'hhLiving', 'hhUtilities', 'hhEducation', 'hhTelecom',
                'hhTransport', 'hhEntertainment', 'hhRent', 'hhOther',
                'bizHPP', 'bizLabor', 'bizTelecom', 'bizTransport',
                'bizUtilities', 'bizRent', 'bizOther',
                'otherExp',
                // Neraca fields
                'gold', 'receivables', 'otherAssets',
                'liabThirdParty', 'liabBPR', 'liabOther',
                'equityOwn', 'profitCurrent', 'profitPast'
            ],
            modifiedFields: {},
            modifiedAssets: {},

            // Collateral State
            collaterals: [
                @if(old('collaterals'))
                    @foreach(old('collaterals') as $col)
                        @json($col),
                    @endforeach
                @elseif(isset($evaluation) && $evaluation->collaterals)
                    @foreach($evaluation->collaterals as $col)
                                {
                            id: {{ $col->id }},
                            type: '{{ $col->type }}',
                            owner_name: '{{ $col->owner_name }}',
                            owner_ktp: '{{ $col->owner_ktp }}',
                            proof_type: '{{ $col->proof_type }}',
                            proof_number: '{{ $col->proof_number }}',
                            proof_date: '{{ $col->proof_date }}',
                            market_value: '{{ number_format($col->market_value, 0, ',', '.') }}',
                            bank_value: '{{ number_format($col->bank_value, 0, ',', '.') }}',

                            // Vehicle Fields
                            brand: '{{ $col->vehicle_brand }}',
                            model: '{{ $col->vehicle_model }}',
                            police_number: '{{ $col->vehicle_plate_number }}',
                            year: '{{ $col->vehicle_year }}',
                            color: '{{ $col->vehicle_color }}',
                            chassis_number: '{{ $col->vehicle_frame_number }}',
                            engine_number: '{{ $col->vehicle_engine_number }}',

                            // Images
                            image_proof: '{{ $col->image_proof }}',
                            vehicle_image_1: '{{ $col->vehicle_image_1 }}',
                            vehicle_image_2: '{{ $col->vehicle_image_2 }}',
                            vehicle_image_3: '{{ $col->vehicle_image_3 }}',
                            vehicle_image_4: '{{ $col->vehicle_image_4 }}',
                            property_image_1: '{{ $col->property_image_1 }}',
                            property_image_2: '{{ $col->property_image_2 }}',
                            property_image_3: '{{ $col->property_image_3 }}',
                            property_image_4: '{{ $col->property_image_4 }}',

                            // Certificate Fields
                            land_area: '{{ (float) $col->property_surface_area > 0 ? rtrim(rtrim($col->property_surface_area, '0'), '.') : "" }}',
                            building_area: '{{ (float) $col->property_building_area > 0 ? rtrim(rtrim($col->property_building_area, '0'), '.') : "" }}',
                            location_address: '{{ $col->property_address ?? $col->location_address }}', // Fallback
                            peruntukan_tanah: '{{ $col->peruntukan_tanah }}',
                            lebar_jalan: '{{ (float) $col->lebar_jalan > 0 ? rtrim(rtrim($col->lebar_jalan, '0'), '.') : "" }}',
                            kondisi_bangunan: '{{ $col->kondisi_bangunan }}',
                            material_pondasi: '{{ $col->material_pondasi }}',
                            material_tembok: '{{ $col->material_tembok }}',
                            material_atap: '{{ $col->material_atap }}',
                            material_kusen: '{{ $col->material_kusen }}',
                            material_daun_pintu: '{{ $col->material_daun_pintu }}',

                            // Common
                            location_address: '{{ $col->location_address }}',
                            latitude: '{{ $col->latitude }}',
                            longitude: '{{ $col->longitude }}',
                            village: '{{ $col->village }}',
                            district: '{{ $col->district }}',
                            regency: '{{ $col->regency }}',
                            province: '{{ $col->province }}',
                        },
                    @endforeach
                @endif
            ],
            checkedDocuments: @json(isset($evaluation) && $evaluation->document_checklist ? $evaluation->document_checklist : []),
            showCollateralModal: false,
            collateralSearch: '',
            collateralFilter: 'all',

            // Collateral Map Logic
            activeCollateralIndex: null,
            showCollateralMapModal: false,
            collateralMap: null,
            collateralMarker: null,

            // Form Checklist Logic
            get documentChecklist() {
                const docs = [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)'
                ];

                const marital = this.selectedCustomer?.marital_status;
                if (marital === 'Menikah') {
                    docs.push('KTP Pasangan');
                    docs.push('Surat Nikah');
                } else if (marital === 'Cerai Hidup') {
                    docs.push('Surat Cerai');
                } else if (marital === 'Cerai Mati') {
                    docs.push('Surat Kematian');
                }

                if (parseFloat(this.loanAmount) >= 100000000) {
                    docs.push('NPWP Pemohon');
                }

                if (this.entrepreneurshipStatus === 'Wirausaha' && this.legality === 'Berbadan Usaha') {
                    docs.push('NPWP Badan Usaha');
                    docs.push('SIUP / Izin Usaha');
                    docs.push('TDP');
                }

                const evalYear = this.evaluationDate ? new Date(this.evaluationDate).getFullYear() : new Date().getFullYear();

                let hasPropertyNeedsOwnerId = false;
                let hasProperty = false;
                let hasVehicle = false;

                this.collaterals.forEach(col => {
                    const type = col.type || '';
                    const isProperty = type.includes('Tanah') || type.includes('Bangunan') || type === 'certificate';
                    const isVehicle = type.includes('Kendaraan') || type === 'vehicle';

                    if (isProperty) hasProperty = true;
                    if (isVehicle) hasVehicle = true;

                    const ownerName = (col.owner_name || '').trim().toLowerCase();
                    const custName = (this.selectedCustomer?.name || '').trim().toLowerCase();
                    const spouseName = (this.selectedCustomer?.spouse_name || '').trim().toLowerCase();

                    if (ownerName && ownerName !== custName && ownerName !== spouseName) {
                        if (isProperty) hasPropertyNeedsOwnerId = true;
                    }
                });

                if (hasProperty) {
                    docs.push('Fotokopi Sertifikat Agunan');
                    docs.push(`PBB Aktif (Tahun ${evalYear})`);
                    if (hasPropertyNeedsOwnerId) {
                        if (!docs.includes('KTP Pemilik Sertifikat')) docs.push('KTP Pemilik Sertifikat');
                    }
                }

                if (hasVehicle) {
                    docs.push('BPKB');
                    docs.push('STNK Aktif');
                    docs.push('Gesek nomor rangka');
                }

                return docs;
            },

            // Scoring Components
            scoringComponents: window.scoringComponents || {},
            componentScores: {},

            // Character Scoring (Part 6)
            charCreditBureau: '{{ old('char_credit_bureau', isset($evaluation) ? $evaluation->char_credit_bureau : '') }}',
            charInfoConsistency: '{{ old('char_info_consistency', isset($evaluation) ? $evaluation->char_info_consistency : '') }}',
            charRelationship: '{{ old('char_relationship', isset($evaluation) ? $evaluation->char_relationship : '') }}',
            charStability: '{{ old('char_stability', isset($evaluation) ? $evaluation->char_stability : '') }}',
            charReputation: '{{ old('char_reputation', isset($evaluation) ? $evaluation->char_reputation : '') }}',

            // Capacity Scoring (Part 6)
            capRpc: '{{ old('cap_rpc', isset($evaluation) ? $evaluation->cap_rpc : '') }}',
            capLamaUsaha: '{{ old('cap_lama_usaha', isset($evaluation) ? $evaluation->cap_lama_usaha : '') }}',
            capUsia: '{{ old('cap_usia', isset($evaluation) ? $evaluation->cap_usia : '') }}',
            capPengelolaan: '{{ old('cap_pengelolaan', isset($evaluation) ? $evaluation->cap_pengelolaan : '') }}',

            // Capital Scoring (Part 6)
            capitalDar: '{{ old('capital_dar', isset($evaluation) ? $evaluation->capital_dar : '') }}',
            capitalDer: '{{ old('capital_der', isset($evaluation) ? $evaluation->capital_der : '') }}',

            // Condition Scoring (Part 6)
            condLokasi: '{{ old('cond_lokasi', isset($evaluation) ? $evaluation->cond_lokasi : '') }}',
            condProfit: '{{ old('cond_profit', isset($evaluation) ? $evaluation->cond_profit : '') }}',
            condDscr: '{{ old('cond_dscr', isset($evaluation) ? $evaluation->cond_dscr : '') }}',

            // Collateral Scoring (Part 6)
            colKepemilikan: '{{ old('col_kepemilikan', isset($evaluation) ? $evaluation->col_kepemilikan : '') }}',
            colPeruntukan: '{{ old('col_peruntukan', isset($evaluation) ? $evaluation->col_peruntukan : '') }}',
            colLebarJalan: '{{ old('col_lebar_jalan', isset($evaluation) ? $evaluation->col_lebar_jalan : '') }}',
            colCoverage: '{{ old('col_coverage', isset($evaluation) ? $evaluation->col_coverage : '') }}',
            colMarketable: '{{ old('col_marketable', isset($evaluation) ? $evaluation->col_marketable : '') }}',

            // Part 4: External Loans
            loans: [
                @if(old('external_loans'))
                    @foreach(old('external_loans') as $loan)
                                                        {
                            bank_name: '{{ $loan['bank_name'] ?? '' }}',
                            realization_date: '{{ $loan['realization_date'] ?? '' }}',
                            maturity_date: '{{ $loan['maturity_date'] ?? '' }}',
                            outstanding_balance: '{{ $loan['outstanding_balance'] ?? '' }}',
                            collectibility: '{{ $loan['collectibility'] ?? '' }}',
                            original_amount: '{{ $loan['original_amount'] ?? '' }}',
                            term_months: '{{ $loan['term_months'] ?? '' }}',
                            interest_rate: '{{ $loan['interest_rate'] ?? '' }}',
                            interest_method: '{{ $loan['interest_method'] ?? 'Flat' }}',
                            installment_amount: '{{ $loan['installment_amount'] ?? '' }}',
                        },
                    @endforeach
                @elseif(isset($evaluation) && $evaluation->externalLoans)
                    @foreach($evaluation->externalLoans as $loan)
                                                        {
                            bank_name: '{{ $loan->bank_name }}',
                            realization_date: '{{ $loan->realization_date }}',
                            maturity_date: '{{ $loan->maturity_date }}',
                            outstanding_balance: '{{ number_format($loan->outstanding_balance, 0, ',', '.') }}',
                            collectibility: '{{ $loan->collectibility }}',
                            original_amount: '{{ number_format($loan->original_amount, 0, ',', '.') }}',
                            term_months: '{{ $loan->term_months }}',
                            interest_rate: '{{ $loan->interest_rate }}',
                            interest_method: '{{ $loan->interest_method }}',
                            installment_amount: '{{ number_format($loan->installment_amount, 0, ',', '.') }}',
                        },
                    @endforeach
                @endif
            ],

            // Loan Calculation State
            loanAmount: '{{ old('loan_amount', isset($evaluation) ? $evaluation->loan_amount : 0) }}',
            displayLoanAmount: '',
            loanTerm: '{{ old('loan_term_months', isset($evaluation) ? $evaluation->loan_term_months : 0) }}',
            interestRate: '{{ old('loan_interest_rate', isset($evaluation) ? $evaluation->loan_interest_rate : 0) }}',
            loanType: '{{ old('loan_type', isset($evaluation) ? $evaluation->loan_type : 'Pinjaman Angsuran') }}',
            loanScheme: '{{ old('loan_scheme', isset($evaluation) ? $evaluation->loan_scheme : '') }}',

            // Old Loan State
            oldLoanType: '{{ old('old_loan_type', isset($evaluation) ? $evaluation->old_loan_type : '') }}',
            oldLoanAmount: '{{ old('old_loan_amount', isset($evaluation) ? $evaluation->old_loan_amount : 0) }}',
            displayOldLoanAmount: '',
            oldLoanTerm: '{{ old('old_loan_term_months', isset($evaluation) ? $evaluation->old_loan_term_months : 0) }}',
            oldInterestRate: '{{ old('old_loan_interest_rate', isset($evaluation) ? $evaluation->old_loan_interest_rate : 0) }}',

            // Customer Status Logical
            customerStatus: '{{ old('customer_status', isset($evaluation) ? $evaluation->customer_status : '') }}',
            legality: '{{ old('customer_entreprenuership_legality', isset($evaluation) ? $evaluation->customer_entreprenuership_legality : '') }}',

            // Economic Sector & Non-Bank Third Party
            economicSectors: {{ Js::from($economicSectors) }},
            nonBankThirdParties: {{ Js::from($nonBankThirdParties) }},
            searchEconomicSector: '',
            selectedEconomicSector: null,
            selectedNonBankThirdParty: (() => {
                const initialName = '{{ old('non_bank_third_party', isset($evaluation) ? $evaluation->non_bank_third_party : 'Perseorangan (Penduduk)') }}';
                const parties = {{ Js::from($nonBankThirdParties) }};
                const found = parties.find(p => p.name === initialName || p.code === initialName);
                return found ? String(found.code) : '9000';
            })(),

            // Cash Flow Analysis State
            openingCash: {{ Js::from(old('kas_usaha', (float) ($evaluation->kas_usaha ?? 0))) }},
            openingSavings: {{ Js::from(old('piutang_usaha', (float) ($evaluation->piutang_usaha ?? 0))) }},
            openingGiro: {{ Js::from(old('persediaan', (float) ($evaluation->persediaan ?? 0))) }},

            rpcRatio: {{ Js::from(old('rpc_ratio', $evaluation->rpc_ratio ?? 35)) }},

            salaryBefore: {{ Js::from(old('cash_in_salary_before', (float) ($evaluation->cash_in_salary_before ?? 0))) }},
            salaryAfter: {{ Js::from(old('cash_in_salary_after', (float) ($evaluation->cash_in_salary_after ?? 0))) }},

            businessBefore: {{ Js::from(old('cash_in_business_before', (float) ($evaluation->cash_in_business_before ?? 0))) }},
            businessAfter: {{ Js::from(old('cash_in_business_after', (float) ($evaluation->cash_in_business_after ?? 0))) }},

            // Pendapatan Lainnya (Dynamic Array)
            otherIncomes: (() => {
                const oldIncomes = @json(old('other_incomes'));
                const savedIncomes = @json(isset($evaluation) && $evaluation->cash_in_other_details ? json_decode($evaluation->cash_in_other_details, true) : null);

                if (oldIncomes && Array.isArray(oldIncomes)) {
                    return oldIncomes.map(inc => ({
                        name: inc.name || '',
                        before: inc.before || '',
                        after: inc.after || ''
                    }));
                } else if (savedIncomes && Array.isArray(savedIncomes) && savedIncomes.length > 0) {
                    return savedIncomes.map(inc => ({
                        name: inc.name || '',
                        before: inc.before || '',
                        after: inc.after || ''
                    }));
                }
                // Default fallback
                return [{ name: '', before: '', after: '' }];
            })(),

            get otherInBefore() {
                return this.otherIncomes.reduce((sum, inc) => sum + (parseFloat(inc.before?.toString().replace(/\D/g, '')) || 0), 0);
            },

            get otherInAfter() {
                return this.otherIncomes.reduce((sum, inc) => sum + (parseFloat(inc.after?.toString().replace(/\D/g, '')) || 0), 0);
            },

            cashOutBefore: {{ Js::from(old('cash_out_total_before', (float) ($evaluation->cash_out_total_before ?? 0))) }},
            cashOutAfter: {{ Js::from(old('cash_out_total_after', (float) ($evaluation->cash_out_total_after ?? 0))) }},

            capitalInjection: {{ Js::from(old('capital_injection_amount', (float) ($evaluation->capital_injection_amount ?? 0))) }},

            // Other Bank Installments
            bankBNIBefore: {{ Js::from(old('bank_bni_before', (float) ($evaluation->bank_bni_before ?? 0))) }},
            bankBNIAfter: {{ Js::from(old('bank_bni_after', (float) ($evaluation->bank_bni_after ?? 0))) }},
            bankOtherBefore: {{ Js::from(old('bank_other_before', (float) ($evaluation->bank_other_before ?? 0))) }},
            bankOtherAfter: {{ Js::from(old('bank_other_after', (float) ($evaluation->bank_other_after ?? 0))) }},

            // Household Expenses
            hhLivingBefore: {{ Js::from(old('hh_living_before', (float) ($evaluation->hh_living_before ?? 0))) }},
            hhLivingAfter: {{ Js::from(old('hh_living_after', (float) ($evaluation->hh_living_after ?? 0))) }},
            hhUtilitiesBefore: {{ Js::from(old('hh_utilities_before', (float) ($evaluation->hh_utilities_before ?? 0))) }},
            hhUtilitiesAfter: {{ Js::from(old('hh_utilities_after', (float) ($evaluation->hh_utilities_after ?? 0))) }},
            hhEducationBefore: {{ Js::from(old('hh_education_before', (float) ($evaluation->hh_education_before ?? 0))) }},
            hhEducationAfter: {{ Js::from(old('hh_education_after', (float) ($evaluation->hh_education_after ?? 0))) }},
            hhTelecomBefore: {{ Js::from(old('hh_telecom_before', (float) ($evaluation->hh_telecom_before ?? 0))) }},
            hhTelecomAfter: {{ Js::from(old('hh_telecom_after', (float) ($evaluation->hh_telecom_after ?? 0))) }},
            hhTransportBefore: {{ Js::from(old('hh_transport_before', (float) ($evaluation->hh_transport_before ?? 0))) }},
            hhTransportAfter: {{ Js::from(old('hh_transport_after', (float) ($evaluation->hh_transport_after ?? 0))) }},
            hhEntertainmentBefore: {{ Js::from(old('hh_entertainment_before', (float) ($evaluation->hh_entertainment_before ?? 0))) }},
            hhEntertainmentAfter: {{ Js::from(old('hh_entertainment_after', (float) ($evaluation->hh_entertainment_after ?? 0))) }},
            hhRentBefore: {{ Js::from(old('hh_rent_before', (float) ($evaluation->hh_rent_before ?? 0))) }},
            hhRentAfter: {{ Js::from(old('hh_rent_after', (float) ($evaluation->hh_rent_after ?? 0))) }},
            hhOtherBefore: {{ Js::from(old('hh_other_before', (float) ($evaluation->hh_other_before ?? 0))) }},
            hhOtherAfter: {{ Js::from(old('hh_other_after', (float) ($evaluation->hh_other_after ?? 0))) }},

            // Business Expenses
            bizHPPBefore: {{ Js::from(old('biz_hpp_before', (float) ($evaluation->biz_hpp_before ?? 0))) }},
            bizHPPAfter: {{ Js::from(old('biz_hpp_after', (float) ($evaluation->biz_hpp_after ?? 0))) }},
            bizLaborBefore: {{ Js::from(old('biz_labor_before', (float) ($evaluation->biz_labor_before ?? 0))) }},
            bizLaborAfter: {{ Js::from(old('biz_labor_after', (float) ($evaluation->biz_labor_after ?? 0))) }},
            bizTelecomBefore: {{ Js::from(old('biz_telecom_before', (float) ($evaluation->biz_telecom_before ?? 0))) }},
            bizTelecomAfter: {{ Js::from(old('biz_telecom_after', (float) ($evaluation->biz_telecom_after ?? 0))) }},
            bizTransportBefore: {{ Js::from(old('biz_transport_before', (float) ($evaluation->biz_transport_before ?? 0))) }},
            bizTransportAfter: {{ Js::from(old('biz_transport_after', (float) ($evaluation->biz_transport_after ?? 0))) }},
            bizUtilitiesBefore: {{ Js::from(old('biz_utilities_before', (float) ($evaluation->biz_utilities_before ?? 0))) }},
            bizUtilitiesAfter: {{ Js::from(old('biz_utilities_after', (float) ($evaluation->biz_utilities_after ?? 0))) }},
            bizRentBefore: {{ Js::from(old('biz_rent_before', (float) ($evaluation->biz_rent_before ?? 0))) }},
            bizRentAfter: {{ Js::from(old('biz_rent_after', (float) ($evaluation->biz_rent_after ?? 0))) }},
            bizOtherBefore: {{ Js::from(old('biz_other_before', (float) ($evaluation->biz_other_before ?? 0))) }},
            bizOtherAfter: {{ Js::from(old('biz_other_after', (float) ($evaluation->biz_other_after ?? 0))) }},

            // Other Expenses Summary
            otherExpBefore: {{ Js::from(old('other_expenses_before', (float) ($evaluation->other_expenses_before ?? 0))) }},
            otherExpAfter: {{ Js::from(old('other_expenses_after', (float) ($evaluation->other_expenses_after ?? 0))) }},

            // Credit Realization (f)
            loanStampDuty: {{ Js::from(old('loan_duty_stamp_cost', (float) ($evaluation->loan_duty_stamp_cost ?? 0))) }},
            loanNotary: {{ Js::from(old('loan_notary_public_cost', (float) ($evaluation->loan_notary_public_cost ?? 0))) }},
            loanInsurance: {{ Js::from(old('loan_insurance_cost', (float) ($evaluation->loan_insurance_cost ?? 0))) }},
            loanOtherCost: {{ Js::from(old('loan_other_cost', (float) ($evaluation->loan_other_cost ?? 0))) }},
            loanProvisionAmount: {{ Js::from(old('loan_provision_cost', (float) ($evaluation->loan_provision_cost ?? 0))) }},
            loanAdminAmount: {{ Js::from(old('loan_administration_cost', (float) ($evaluation->loan_administration_cost ?? 0))) }},

            // Editable Rates State
            loanProvisionRate: {{ Js::from(old('loan_provision_rate', $evaluation->loan_provision_rate ?? '')) }},
            loanAdminRate: {{ Js::from(old('loan_admin_rate', $evaluation->loan_admin_rate ?? '')) }},
            isProvisionManual: false,
            isAdminManual: false,

            // Part 3: Assets & Liabilities
            customAssetsList: [
                @if(isset($evaluation) && $evaluation->customAssets->count() > 0)
                    @foreach($evaluation->customAssets as $asset)
                        { name: '{{ $asset->name }}', type: '{{ $asset->type }}', estimated_price: '{{ intval($asset->estimated_price) }}' },
                    @endforeach
                @elseif(old('custom_assets'))
                    @foreach(old('custom_assets') as $idx => $asset)
                        { name: '{{ old("custom_assets.$idx.name", "") }}', type: '{{ old("custom_assets.$idx.type", "") }}', estimated_price: '{{ old("custom_assets.$idx.estimated_price", "") }}' },
                    @endforeach
                @else
                    { name: '', type: '', estimated_price: '' }
                @endif
            ],

            addCustomAsset() {
                this.customAssetsList.push({ name: '', type: '', estimated_price: '' });
            },

            removeCustomAsset(index) {
                if (this.customAssetsList.length > 1) {
                    this.customAssetsList.splice(index, 1);
                } else {
                    this.customAssetsList = [{ name: '', type: '', estimated_price: '' }];
                }
            },

            get totalCustomAssets() {
                return this.customAssetsList.reduce((sum, item) => {
                    const price = String(item.estimated_price || 0).replace(/\D/g, '');
                    return sum + (parseFloat(price) || 0);
                }, 0);
            },

            // Part 7: Guarantors (Penjamin)
            guarantorsList: [
                @if(isset($evaluation) && $evaluation->guarantors->count() > 0)
                    @foreach($evaluation->guarantors as $guarantor)
                        { name: '{{ $guarantor->name }}', relationship: '{{ $guarantor->relationship }}' },
                    @endforeach
                @elseif(old('guarantors'))
                    @foreach(old('guarantors') as $idx => $guarantor)
                        { name: '{{ old("guarantors.$idx.name", "") }}', relationship: '{{ old("guarantors.$idx.relationship", "") }}' },
                    @endforeach
                @else
                    { name: '', relationship: '' }
                @endif
            ],

            addGuarantor() {
                this.guarantorsList.push({ name: '', relationship: '' });
            },

            removeGuarantor(index) {
                if (this.guarantorsList.length > 1) {
                    this.guarantorsList.splice(index, 1);
                } else {
                    this.guarantorsList = [{ name: '', relationship: '' }];
                }
            },

            // Ratios & Analysis
            get dsrRatio() {
                const limit = parseFloat(this.monthlyInstallment) || 0;
                const bankInstallment = parseFloat(this.bankInstallmentsBefore) || 0;
                const netCashFlow = parseFloat(this.netCashFlowBefore) || 0;

                if (netCashFlow <= 0) return 0;

                const result = (((limit + bankInstallment) / netCashFlow) * 100).toFixed(2);
                console.log(`DSR Calculation: ((${limit} [recMonthlyInstallment] + ${bankInstallment} [bankInstallment]) / ${netCashFlow} [netCashFlow]) * 100 = ${result}%`);

                return result;
            },

            get darRatio() {
                // DAR (Debt-to-Asset Ratio) = Total Outstanding Balance of External Loans / Total Custom Assets
                const totalAssets = this.totalCustomAssets;

                // Sum outstanding balances from all external loans
                const totalExternalLoans = this.loans.reduce((sum, loan) => {
                    const balance = String(loan.outstanding_balance || 0).replace(/\D/g, '');
                    return sum + (parseFloat(balance) || 0);
                }, 0);

                if (totalAssets <= 0) return 0; // Avoid division by zero

                return ((totalExternalLoans / totalAssets) * 100).toFixed(2);
            },

            get derRatio() {
                // DER (Debt-to-Equity Ratio) = Total External Loans / Modal Usaha
                const totalEquity = this.modalUsaha;

                // Sum outstanding balances from all external loans
                const totalExternalLoans = this.loans.reduce((sum, loan) => {
                    const balance = String(loan.outstanding_balance || 0).replace(/\D/g, '');
                    return sum + (parseFloat(balance) || 0);
                }, 0);

                if (totalEquity <= 0) return 0; // Avoid division by zero

                return ((totalExternalLoans / totalEquity) * 100).toFixed(2);
            },

            get dtiRatio() {
                // DTI = Total Cash In / Total External Installment
                const totalIncome = parseFloat(this.cashInTotalBefore) || 0;

                // Sum installment amounts from all external loans using reduce
                const externalInstallments = this.loans.reduce((sum, loan) => {
                    const installment = String(loan.installment_amount || 0).replace(/\D/g, '');
                    return sum + (parseFloat(installment) || 0);
                }, 0);

                if (externalInstallments <= 0) return 0; // Prevent division by zero if there's no installment

                return ((externalInstallments / totalIncome) * 100).toFixed(2);
            },

            // Liabilities & Equity

            // Liabilities & Equity
            liabThirdPartyBefore: {{ Js::from(old('liab_third_party_before', (float) ($evaluation->liab_third_party_before ?? 0))) }},
            liabThirdPartyAfter: {{ Js::from(old('liab_third_party_after', (float) ($evaluation->liab_third_party_after ?? 0))) }},

            liabBPRBefore: {{ Js::from(old('liab_bpr_before', (float) ($evaluation->liab_bpr_before ?? 0))) }},
            liabBPRAfter: {{ Js::from(old('liab_bpr_after', (float) ($evaluation->liab_bpr_after ?? 0))) }},

            liabOtherBefore: {{ Js::from(old('liab_other_before', (float) ($evaluation->liab_other_before ?? 0))) }},
            liabOtherAfter: {{ Js::from(old('liab_other_after', (float) ($evaluation->liab_other_after ?? 0))) }},

            equityOwnBefore: {{ Js::from(old('equity_own_before', (float) ($evaluation->equity_own_before ?? 0))) }},
            equityOwnAfter: {{ Js::from(old('equity_own_after', (float) ($evaluation->equity_own_after ?? 0))) }},

            profitCurrentBefore: {{ Js::from(old('profit_current_before', (float) ($evaluation->profit_current_before ?? 0))) }},
            profitCurrentAfter: {{ Js::from(old('profit_current_after', (float) ($evaluation->profit_current_after ?? 0))) }},

            profitPastBefore: {{ Js::from(old('profit_past_before', (float) ($evaluation->profit_past_before ?? 0))) }},
            profitPastAfter: {{ Js::from(old('profit_past_after', (float) ($evaluation->profit_past_after ?? 0))) }},


            kewajibanLancar: {{ Js::from(old('kewajiban_lancar', (float) ($evaluation->kewajiban_lancar ?? 0))) }},
            kewajibanJangkaPanjang: {{ Js::from(old('kewajiban_jangka_panjang', (float) ($evaluation->kewajiban_jangka_panjang ?? 0))) }},

            // Scoring State
            componentScores: {
                @if(old('scores'))
                    @foreach(old('scores') as $id => $scoreData)
                        {{ $id }}: {{ $scoreData['score'] }},
                    @endforeach
                @elseif(isset($evaluation) && $evaluation->scores)
                @foreach($evaluation->scores as $score)
                    {{ $score->credit_scoring_component_id }}: {{ $score->score }},
                @endforeach
            @endif
            },

        get totalScore() {
        let total = 0;
        const components = this.activeScoringComponents;

        // activeScoringComponents is an object grouped by category
        // { 'Character': [ {id:1, weight:10, ...}, ... ], ... }

        for(const category in components) {
            const items = components[category];
            items.forEach(item => {
                const score = parseFloat(this.componentScores[item.id]) || 0;
                const weight = parseFloat(item.weight) || 0;
                total += (score * (weight / 100)); // Weighted Score? Or just Score * Weight? 
                // Usually Scoring is: (Score * Weight%)
                // But wait, if max score is 5, and total weight is 100%. Max Total should be 5.
                // Let's assume Weighted Score = Score * (Weight/100).
            });
        }
                return total.toFixed(2);
    },

        get creditScoreStatus() {
            const score = parseFloat(this.totalScore);
            if(score > 4.6) return 'Sangat Layak';
    if (score > 3.6) return 'Layak';
    if (score > 2.8) return 'Cukup Layak';
    if (score > 1.8) return 'Kurang Layak';
    return 'Tidak Layak';
            },

            get creditScoreStatusColor() {
        const score = parseFloat(this.totalScore);
        if (score > 4.6) return 'text-green-600 bg-green-100';
        if (score > 3.6) return 'text-blue-600 bg-blue-100';
        if (score > 2.8) return 'text-yellow-600 bg-yellow-100';
        if (score > 1.8) return 'text-orange-600 bg-orange-100';
        return 'text-red-600 bg-red-100';
    },

    init() {
        this.displayLoanAmount = this.formatNumber(this.loanAmount);
        this.displayOldLoanAmount = this.formatNumber(this.oldLoanAmount);

        // Initialize Sync State: Detect if 'After' fields are already different from 'Before'
        this.syncFields.forEach(base => {
            const before = this[base + 'Before'];
            const after = this[base + 'After'];
            // Compare values loosely to handle string/number differences. 
            // If they differ, mark 'After' as modified so we don't overwrite it.
            if (before != after) {
                this.modifiedFields[base + 'After'] = true;
            }
        });

        // Initialize Economic Sector
        const oldSectorName = {{ Js::from(old('economic_sector', isset($evaluation) ? $evaluation->economic_sector : '')) }};

        if (oldSectorName) {
            this.searchEconomicSector = oldSectorName;
            if (this.economicSectors.length > 0) {
                this.selectedEconomicSector = this.economicSectors.find(s => s.name === oldSectorName);
            }
        }

        // Watchers for Economic Sector
        this.$watch('selectedEconomicSector', (value) => {
            if (value) {
                this.searchEconomicSector = value.name;
            }
        });

        // Initialize Rates
        const hasProvision = this.loanProvisionRate !== '';
        const hasAdmin = this.loanAdminRate !== '';

        if (hasProvision) {
            this.isProvisionManual = true;
        }
        if (hasAdmin) {
            this.isAdminManual = true;
        }

        // Only update from term if at least one rate is missing (not manual)
        if (!hasProvision || !hasAdmin) {
            this.updateRatesFromTerm();
        }

        // Initialize provision and admin amounts if 0
        if (this.loanProvisionAmount == 0) this.loanProvisionAmount = this.calcDefaultProvision();
        if (this.loanAdminAmount == 0) this.loanAdminAmount = this.calcDefaultAdmin();

        this.$watch('loanAmount', () => {
            this.loanProvisionAmount = this.calcDefaultProvision();
            if (this.loanScheme === 'Modal Kerja') {
                this.capitalInjection = this.loanAmount;
            }
            this.loanAdminAmount = this.calcDefaultAdmin();
        });

        this.$watch('loanScheme', (value) => {
            if (value === 'Modal Kerja') {
                this.capitalInjection = this.loanAmount;
            }
        });

        this.$watch('loanTerm', () => {
            this.updateRatesFromTerm();
            this.loanProvisionAmount = this.calcDefaultProvision();
            this.loanAdminAmount = this.calcDefaultAdmin();
        });

        this.$watch('loanProvisionRate', () => {
            this.loanProvisionAmount = this.calcDefaultProvision();
        });

        this.$watch('loanAdminRate', () => {
            this.loanAdminAmount = this.calcDefaultAdmin();
        });
    },

    formatNumber(value) {
        if (value === null || value === undefined || value === '') return '';
        return new Intl.NumberFormat('id-ID').format(value);
    },

    updateLoanAmount(value) {
        const numericValue = value.replace(/\D/g, '');
        this.loanAmount = numericValue;
        this.displayLoanAmount = this.formatNumber(numericValue);
    },

    updateCFValue(field, value) {
        const numericValue = value.replace(/\D/g, '');
        this[field] = numericValue;

        // Sync Logic: Automatically update 'After' if 'Before' changes, unless 'After' was modified manually
        if (field.endsWith('Before')) {
            const base = field.replace('Before', '');
            if (this.syncFields.includes(base)) {
                const targetField = base + 'After';
                // If the target 'After' field hasn't been modified manually, sync it
                if (!this.modifiedFields[targetField]) {
                    this[targetField] = numericValue;
                }
            }
        } else if (field.endsWith('After')) {
            const base = field.replace('After', '');
            if (this.syncFields.includes(base)) {
                // Mark as modified so it stops syncing with 'Before'
                this.modifiedFields[field] = true;
            }
        }
    },

    addOtherIncome() {
        this.otherIncomes.push({ name: '', before: '', after: '' });
    },

    removeOtherIncome(index) {
        if (this.otherIncomes.length > 1) {
            this.otherIncomes.splice(index, 1);
        } else {
            this.otherIncomes[0] = { name: '', before: '', after: '' };
        }
    },

    updateOtherIncomeCFValue(index, field, value) {
        const numericValue = value.toString().replace(/\D/g, '');
        this.otherIncomes[index][field] = numericValue;

        if (field === 'before') {
            // Sync 'after' if it wasn't set or if we're typing from scratch
            // We can be simple: always sync if 'after' is empty or matches old 'before'
            // For safety, just set it if we want naive sync.
            this.otherIncomes[index]['after'] = numericValue;
        }
    },

            get activeScoringComponents() {
        return this.scoringComponents[this.loanScheme] || {};
    },

            // Character Scoring Computed Properties
            get worstCollectibility() {
        if (!this.loans || this.loans.length === 0) return 5;
        const collectMap = {
            'Lancar': 5, 'DPK': 4, 'Kurang Lancar': 3, 'Diragukan': 2, 'Macet': 1
        };
        let worst = 5;
        this.loans.forEach(loan => {
            if (loan.collectibility && collectMap[loan.collectibility] !== undefined) {
                const val = collectMap[loan.collectibility];
                if (val < worst) worst = val;
            }
        });
        return worst;
    },

            get worstCollectibilityLabel() {
        const labelMap = { 5: 'Lancar', 4: 'DPK', 3: 'Kurang Lancar', 2: 'Diragukan', 1: 'Macet' };
        return labelMap[this.worstCollectibility] || '-';
    },

            get badCollectibilityLoans() {
        if (!this.loans || this.loans.length === 0) return [];
        const worstLabel = this.worstCollectibilityLabel;
        return this.loans.filter(loan => loan.collectibility === worstLabel);
    },

            get badCollectibilityTotalOutstanding() {
        return this.badCollectibilityLoans.reduce((sum, loan) => {
            return sum + (parseFloat(loan.outstanding_balance?.toString().replace(/\D/g, '')) || 0);
        }, 0);
    },

            get charRelationshipAuto() {
        if (!this.customerStatus) return 1;
        return this.customerStatus.toLowerCase().includes('lama') ? 2 : 1;
    },

            get charStabilityFromYears() {
        const years = parseFloat(document.getElementById('customer_company_years')?.value) || 0;
        if (years >= 6) return 4;
        if (years >= 4) return 3;
        if (years >= 2) return 2;
        return 1;
    },

            get capUsiaAuto() {
        if (!this.selectedCustomer || !this.selectedCustomer.dob) return 5;
        const termMonths = parseFloat(this.loanTerm) || 0;
        if (termMonths <= 0) return 5;
        const dob = new Date(this.selectedCustomer.dob);
        const today = new Date();
        const maturityDate = new Date(today);
        maturityDate.setMonth(maturityDate.getMonth() + termMonths);
        let ageAtMaturity = maturityDate.getFullYear() - dob.getFullYear();
        const m = maturityDate.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && maturityDate.getDate() < dob.getDate())) ageAtMaturity--;
        if (ageAtMaturity <= 40) return 5;
        if (ageAtMaturity <= 50) return 4;
        if (ageAtMaturity <= 55) return 3;
        if (ageAtMaturity <= 60) return 2;
        return 1;
    },

            get capitalDarAuto() {
        const dar = parseFloat(this.darRatio) || 0;
        if (dar < 20) return 5;
        if (dar <= 30) return 4;
        if (dar <= 40) return 3;
        if (dar <= 50) return 2;
        return 1;
    },

            get capitalDerAuto() {
        const der = parseFloat(this.derRatio) || 0;
        if (der < 100) return 5;
        if (der <= 150) return 4;
        if (der <= 200) return 3;
        if (der <= 250) return 2;
        return 1;
    },

            get condLokasiAuto() {
        let totalDist = 0;
        let count = 0;
        this.collaterals.forEach((col, index) => {
            const el = document.getElementById('collateral-path-distance-' + index);
            const dist = parseFloat(el?.value) || 0;
            if (dist > 0) { totalDist += dist; count++; }
        });
        if (count === 0) return 5;
        const avg = totalDist / count;
        if (avg < 12) return 5;
        if (avg <= 20) return 4;
        if (avg <= 30) return 3;
        if (avg <= 40) return 2;
        return 1;
    },

            get condDscrAuto() {
        const dsr = parseFloat(this.dsrRatio) || 0;
        if (dsr === 0) return 5;
        if (dsr < 30) return 5;
        if (dsr >= 30 && dsr < 51) return 4;
        if (dsr >= 51 && dsr < 70) return 3;
        if (dsr >= 70 && dsr <= 80) return 2;
        return 1;
    },

            get colKepemilikanAuto() {
        if (!this.collaterals || this.collaterals.length === 0) return 5;
        const custName = (this.selectedCustomer?.name || '').trim().toLowerCase();
        const spouseName = (this.selectedCustomer?.spouse_name || '').trim().toLowerCase();
        if (!custName) return null;
        const allOwnedBySelf = this.collaterals.every(col => {
            const owner = (col.owner_name || '').trim().toLowerCase();
            return owner === '' || owner === custName || (spouseName && owner === spouseName);
        });
        return allOwnedBySelf ? 5 : null;
    },

            get colCoverageAuto() {
        const loanAmt = parseFloat(String(this.loanAmount).replace(/\D/g, '')) || 0;
        if (loanAmt <= 0) return 5;
        const totalBank = this.totalCollateralBankValue;
        if (totalBank <= 0) return 1;
        const coverage = (totalBank / loanAmt) * 100;
        if (coverage > 150) return 5;
        if (coverage >= 130) return 4;
        if (coverage >= 110) return 3;
        if (coverage >= 100) return 2;
        return 1;
    },

            get characterTotalScore() {
        const bureau = parseInt(this.charCreditBureau) || 0;
        const info = parseInt(this.charInfoConsistency) || 0;
        const rel = parseInt(this.charRelationship) || 0;
        const stab = parseInt(this.charStability) || 0;
        const rep = parseInt(this.charReputation) || 0;

        // Max scores: bureau=5, info=4, rel=2, stab=4, rep=5
        // Weighted: (score/maxScore) * weight * 100
        const total = (bureau / 5 * 25) + (info / 4 * 20) + (rel / 2 * 10) + (stab / 4 * 20) + (rep / 5 * 25);
        return total.toFixed(2);
    },

            get characterScoreStatus() {
        const score = parseFloat(this.characterTotalScore);
        if (score >= 80) return 'Sangat Baik';
        if (score >= 60) return 'Baik';
        if (score >= 40) return 'Cukup';
        if (score >= 20) return 'Kurang';
        return 'Buruk';
    },

            get characterScoreStatusColor() {
        const score = parseFloat(this.characterTotalScore);
        if (score >= 80) return 'text-green-700 bg-green-100 border-green-300';
        if (score >= 60) return 'text-blue-700 bg-blue-100 border-blue-300';
        if (score >= 40) return 'text-yellow-700 bg-yellow-100 border-yellow-300';
        if (score >= 20) return 'text-orange-700 bg-orange-100 border-orange-300';
        return 'text-red-700 bg-red-100 border-red-300';
    },

            // Employee vs Entrepreneur detection
            get isEntrepreneur() {
        return this.entrepreneurshipStatus === 'Wirausaha';
    },

            // Employee-specific scoring getters
            get capMasaKerjaAuto() {
        const years = parseFloat(document.getElementById('customer_company_years')?.value) || 0;
        if (years <= 0) return '';
        if (years > 5) return '5';
        if (years >= 3) return '4';
        if (years >= 2) return '3';
        if (years >= 1) return '2';
        return '1';
    },

            get capStatusKepegawaianAuto() {
        const status = this.employmentStatus;
        if (!status || status === 'Bukan Karyawan') return '';
        if (status === 'PNS') return '5';
        if (status === 'TNI/Polri') return '4';
        if (status === 'BUMN') return '3';
        if (status === 'Swasta') return '2';
        return '';
    },

            get condStabilitasAuto() {
        const freq = this.salaryFrequency;
        if (!freq) return '';
        if (freq === 'Bulanan') return '5';
        if (freq === 'Mingguan') return '4';
        if (freq === 'Harian') return '3';
        if (freq === 'Borongan') return '2';
        return '1';
    },

            get condJaminanAuto() {
        const status = this.employmentStatus;
        if (!status || status === 'Bukan Karyawan') return '';
        if (status === 'PNS') return '5';
        if (status === 'TNI/Polri') return '4';
        if (status === 'BUMN') return '3';
        if (status === 'Swasta') return '2';
        return '';
    },

            // Capacity Scoring Computed Properties
            get capRpcAuto() {
        const rpc = parseFloat(this.rpcRatio) || 0;
        if (rpc === 0) return '';
        if (rpc < 30) return '5';
        if (rpc >= 30 && rpc < 51) return '4';
        if (rpc >= 51 && rpc < 70) return '3';
        if (rpc >= 70 && rpc <= 80) return '2';
        if (rpc > 80) return '1';
        return '5';
    },

            get capLamaUsahaAuto() {
        const foundingYear = parseInt(document.getElementById('customer_entreprenuership_year')?.value) || 0;
        if (foundingYear <= 0) return 5;
        const currentYear = new Date().getFullYear();
        const years = currentYear - foundingYear;
        if (years >= 5) return 5;
        if (years >= 4 && years < 5) return 4;
        if (years >= 3 && years < 4) return 3;
        if (years >= 2 && years < 3) return 2;
        return 1;
    },

            get capacityTotalScore() {
        const rpc = parseInt(this.capRpc) || 0;
        const lamaUsaha = parseInt(this.capLamaUsaha) || 0;
        const usia = parseInt(this.capUsia) || 0;
        const pengelolaan = parseInt(this.capPengelolaan) || 0;

        const total = (rpc / 5 * 40) + (lamaUsaha / 5 * 20) + (usia / 5 * 20) + (pengelolaan / 5 * 20);
        return total.toFixed(2);
    },

            get capacityScoreStatus() {
        const score = parseFloat(this.capacityTotalScore);
        if (score >= 80) return 'Sangat Baik';
        if (score >= 60) return 'Baik';
        if (score >= 40) return 'Cukup';
        if (score >= 20) return 'Kurang';
        return 'Buruk';
    },

            get capacityScoreStatusColor() {
        const score = parseFloat(this.capacityTotalScore);
        if (score >= 80) return 'text-green-700 bg-green-100 border-green-300';
        if (score >= 60) return 'text-blue-700 bg-blue-100 border-blue-300';
        if (score >= 40) return 'text-yellow-700 bg-yellow-100 border-yellow-300';
        if (score >= 20) return 'text-orange-700 bg-orange-100 border-orange-300';
        return 'text-red-700 bg-red-100 border-red-300';
    },

            // Capital Computed Properties
            get capitalTotalScore() {
        const dar = parseInt(this.capitalDar) || 0;
        const der = parseInt(this.capitalDer) || 0;
        const total = (dar / 5 * 40) + (der / 5 * 60);
        return total.toFixed(2);
    },
            get capitalScoreStatus() {
        const score = parseFloat(this.capitalTotalScore);
        if (score >= 80) return 'Sangat Baik';
        if (score >= 60) return 'Baik';
        if (score >= 40) return 'Cukup';
        if (score >= 20) return 'Kurang';
        return 'Buruk';
    },
            get capitalScoreStatusColor() {
        const score = parseFloat(this.capitalTotalScore);
        if (score >= 80) return 'text-green-700 bg-green-100 border-green-300';
        if (score >= 60) return 'text-blue-700 bg-blue-100 border-blue-300';
        if (score >= 40) return 'text-yellow-700 bg-yellow-100 border-yellow-300';
        if (score >= 20) return 'text-orange-700 bg-orange-100 border-orange-300';
        return 'text-red-700 bg-red-100 border-red-300';
    },

            // Condition Computed Properties
            get conditionTotalScore() {
        const lokasi = parseInt(this.condLokasi) || 0;
        const profit = parseInt(this.condProfit) || 0;
        const dscr = parseInt(this.condDscr) || 0;
        const total = (lokasi / 5 * 20) + (profit / 5 * 20) + (dscr / 5 * 60);
        return total.toFixed(2);
    },
            get conditionScoreStatus() {
        const score = parseFloat(this.conditionTotalScore);
        if (score >= 80) return 'Sangat Baik';
        if (score >= 60) return 'Baik';
        if (score >= 40) return 'Cukup';
        if (score >= 20) return 'Kurang';
        return 'Buruk';
    },
            get conditionScoreStatusColor() {
        const score = parseFloat(this.conditionTotalScore);
        if (score >= 80) return 'text-green-700 bg-green-100 border-green-300';
        if (score >= 60) return 'text-blue-700 bg-blue-100 border-blue-300';
        if (score >= 40) return 'text-yellow-700 bg-yellow-100 border-yellow-300';
        if (score >= 20) return 'text-orange-700 bg-orange-100 border-orange-300';
        return 'text-red-700 bg-red-100 border-red-300';
    },

            // Collateral Computed Properties
            get collateralTotalScore() {
        const kep = parseInt(this.colKepemilikan) || 0;
        const per = parseInt(this.colPeruntukan) || 0;
        const jalan = parseInt(this.colLebarJalan) || 0;
        const cov = parseInt(this.colCoverage) || 0;
        const mark = parseInt(this.colMarketable) || 0;
        const total = (kep / 5 * 20) + (per / 5 * 10) + (jalan / 5 * 20) + (cov / 5 * 30) + (mark / 5 * 20);
        return total.toFixed(2);
    },
            get collateralScoreStatus() {
        const score = parseFloat(this.collateralTotalScore);
        if (score >= 80) return 'Sangat Baik';
        if (score >= 60) return 'Baik';
        if (score >= 40) return 'Cukup';
        if (score >= 20) return 'Kurang';
        return 'Buruk';
    },
            get collateralScoreStatusColor() {
        const score = parseFloat(this.collateralTotalScore);
        if (score >= 80) return 'text-green-700 bg-green-100 border-green-300';
        if (score >= 60) return 'text-blue-700 bg-blue-100 border-blue-300';
        if (score >= 40) return 'text-yellow-700 bg-yellow-100 border-yellow-300';
        if (score >= 20) return 'text-orange-700 bg-orange-100 border-orange-300';
        return 'text-red-700 bg-red-100 border-red-300';
    },

            // --- 5C Combined Final Score ---
            // Convert 0-100 category scores to 1-5 scale "Nilai"
            get characterNilai() { return (parseFloat(this.characterTotalScore) / 100 * 5).toFixed(1); },
            get capacityNilai() { return (parseFloat(this.capacityTotalScore) / 100 * 5).toFixed(1); },
            get capitalNilai() { return (parseFloat(this.capitalTotalScore) / 100 * 5).toFixed(1); },
            get conditionNilai() { return (parseFloat(this.conditionTotalScore) / 100 * 5).toFixed(1); },
            get collateralNilai() { return (parseFloat(this.collateralTotalScore) / 100 * 5).toFixed(1); },

            // Weighted scale: (Nilai / 5) * categoryWeight
            get characterSkala() { return (parseFloat(this.characterNilai) / 5 * 30).toFixed(2); },
            get capacitySkala() { return (parseFloat(this.capacityNilai) / 5 * 20).toFixed(2); },
            get capitalSkala() { return (parseFloat(this.capitalNilai) / 5 * 20).toFixed(2); },
            get conditionSkala() { return (parseFloat(this.conditionNilai) / 5 * 10).toFixed(2); },
            get collateralSkala() { return (parseFloat(this.collateralNilai) / 5 * 20).toFixed(2); },

            // Combined Kesimpulan score (weighted sum of all Skala / 100 * 5)
            get finalScore() {
        const total = parseFloat(this.characterSkala) + parseFloat(this.capacitySkala) + parseFloat(this.capitalSkala) + parseFloat(this.conditionSkala) + parseFloat(this.collateralSkala);
        return (total / 100 * 5).toFixed(2);
    },

            get finalScoreKelayakan() {
        const s = parseFloat(this.finalScore);
        if (s >= 4.61) return 'Sangat Layak';
        if (s >= 3.6) return 'Layak';
        if (s >= 2.81) return 'Cukup Layak';
        if (s >= 1.81) return 'Kurang Layak';
        return 'Tidak Layak';
    },

            get finalScoreColor() {
        const s = parseFloat(this.finalScore);
        if (s >= 4.61) return 'text-green-800 bg-green-100 border-green-400';
        if (s >= 3.6) return 'text-blue-800 bg-blue-100 border-blue-400';
        if (s >= 2.81) return 'text-yellow-800 bg-yellow-100 border-yellow-400';
        if (s >= 1.81) return 'text-orange-800 bg-orange-100 border-orange-400';
        return 'text-red-800 bg-red-100 border-red-400';
    },


            get openingBalance() {
        return (parseFloat(this.openingCash) || 0) +
            (parseFloat(this.openingSavings) || 0) +
            (parseFloat(this.openingGiro) || 0);
    },

            get cashInTotalBefore() {
        return (parseFloat(this.salaryBefore) || 0) +
            (parseFloat(this.businessBefore) || 0) +
            (parseFloat(this.otherInBefore) || 0);
    },

            get cashInTotalAfter() {
        return (parseFloat(this.salaryAfter) || 0) +
            (parseFloat(this.businessAfter) || 0) +
            (parseFloat(this.otherInAfter) || 0) +
            (parseFloat(this.capitalInjection) || 0);
    },

            get opOpeningTotalBefore() {
        return (this.openingBalance);
    },

            get opOpeningTotalAfter() {
        // For 'After', typically it's openingBalance + cashInTotalAfter, OR
        // is it the endOpBalanceBefore carried over?
        // The user said: "a. Saldo Awal Operasional si calculated result of Saldo Awal Kas & Bank + result calculated of b. Arus Kas Masuk"
        // Assuming consistency for both columns.
        return (this.endOpBalanceBefore) - (this.rpcTotalBefore);
    },

            // Expenses Calculations
            get totalExternalInstallment() {
        return this.loans.reduce((sum, loan) => {
            const inst = parseFloat(loan.installment_amount?.toString().replace(/\D/g, '')) || 0;
            return sum + inst;
        }, 0);
    },

            get totalExternalLoanOutstanding() {
        return this.loans.reduce((sum, loan) => {
            const out = parseFloat(loan.outstanding_balance?.toString().replace(/\D/g, '')) || 0;
            return sum + out;
        }, 0);
    },

            get bankInstallmentsBefore() {
        return (parseFloat(this.bankBNIBefore) || 0) + this.totalExternalInstallment;
    },
            get bankInstallmentsAfter() {
        return (parseFloat(this.bankBNIAfter) || 0) + this.totalExternalInstallment;
    },

            get hhTotalBefore() {
        return (parseFloat(this.hhLivingBefore) || 0) + (parseFloat(this.hhUtilitiesBefore) || 0) +
            (parseFloat(this.hhEducationBefore) || 0) + (parseFloat(this.hhTelecomBefore) || 0) +
            (parseFloat(this.hhTransportBefore) || 0) + (parseFloat(this.hhEntertainmentBefore) || 0) +
            (parseFloat(this.hhRentBefore) || 0) + (parseFloat(this.hhOtherBefore) || 0);
    },
            get hhTotalAfter() {
        return (parseFloat(this.hhLivingAfter) || 0) + (parseFloat(this.hhUtilitiesAfter) || 0) +
            (parseFloat(this.hhEducationAfter) || 0) + (parseFloat(this.hhTelecomAfter) || 0) +
            (parseFloat(this.hhTransportAfter) || 0) + (parseFloat(this.hhEntertainmentAfter) || 0) +
            (parseFloat(this.hhRentAfter) || 0) + (parseFloat(this.hhOtherAfter) || 0);
    },

            get bizTotalBefore() {
        return (parseFloat(this.bizHPPBefore) || 0) + (parseFloat(this.bizLaborBefore) || 0) +
            (parseFloat(this.bizTelecomBefore) || 0) + (parseFloat(this.bizTransportBefore) || 0) +
            (parseFloat(this.bizUtilitiesBefore) || 0) + (parseFloat(this.bizRentBefore) || 0) +
            (parseFloat(this.bizOtherBefore) || 0);
    },
            get bizTotalAfter() {
        return (parseFloat(this.bizHPPAfter) || 0) + (parseFloat(this.bizLaborAfter) || 0) +
            (parseFloat(this.bizTelecomAfter) || 0) + (parseFloat(this.bizTransportAfter) || 0) +
            (parseFloat(this.bizUtilitiesAfter) || 0) + (parseFloat(this.bizRentAfter) || 0) +
            (parseFloat(this.bizOtherAfter) || 0);
    },

            get cashOutTotalBefore() {
        return (this.bankInstallmentsBefore) + (this.hhTotalBefore) + (this.bizTotalBefore) + (parseFloat(this.otherExpBefore) || 0);
    },
            get cashOutTotalAfter() {
        return (this.bankInstallmentsAfter) + (this.hhTotalAfter) + (this.bizTotalAfter) + (parseFloat(this.otherExpAfter) || 0);
    },

            // Summaries
            get labaBerjalan() {
        return (parseFloat(this.businessBefore) || 0) - (parseFloat(this.bizTotalBefore) || 0);
    },
            get netCashFlowBefore() {
        return this.cashInTotalBefore - this.cashOutTotalBefore;
    },
            get netCashFlowAfter() {
        return this.cashInTotalAfter - this.cashOutTotalAfter;
    },

            get endOpBalanceBefore() {
        return this.opOpeningTotalBefore + this.netCashFlowBefore;
    },
            get endOpBalanceAfter() {
        return this.opOpeningTotalAfter + this.netCashFlowAfter;
    },

            get rpcTotalBefore() {
        return Math.round(this.netCashFlowBefore * (parseFloat(this.rpcRatio) / 100));
    },

            get maxLoanLimit() {
        // Formula:
        // Gross Income = cashInTotalBefore
        // Total Expense = cashOutTotalBefore (excluding new loan installment)
        // Net Income = cashInTotalBefore - cashOutTotalBefore
        // RPC Limit = cashInTotalBefore * (rpcRatio / 100)

        const netIncome = this.cashInTotalBefore - this.cashOutTotalBefore;
        const rpcValue = parseFloat(this.rpcRatio) / 100;
        const incomeRpcLimit = this.netCashFlowBefore * rpcValue;

        let numerator;
        if (netIncome > incomeRpcLimit) {
            numerator = incomeRpcLimit;
        } else {
            numerator = netIncome;
        }

        const term = parseFloat(this.loanTerm) || 0;
        const ratePerYear = parseFloat(this.interestRate) || 0;
        const ratePerMonth = ratePerYear / 1200; // (% / 1200 because percentage)

        if (term === 0) return 0;

        let limit = 0;

        if (this.loanType === 'Pinjaman Anuitas') {
            if (ratePerMonth === 0) {
                limit = numerator * term;
            } else {
                // numerator x (1 - ((1 + (loan_term_months%/12))^-loan_tenor)/(loan_term_months%/12))
                const pvFactor = (1 - Math.pow(1 + ratePerMonth, -term)) / ratePerMonth;
                limit = numerator * pvFactor;
            }
        } else if (this.loanType === 'Pinjaman Musiman') {
            // numerator / (loan_term_months%/12)
            if (ratePerMonth > 0) {
                limit = numerator / ratePerMonth;
            } else {
                limit = 0;
            }
        } else {
            // Default to 'Pinjaman Angsuran' if loanType is empty or not matched
            // (numerator x loan_tenor) / (1 + ((loan_term_months%/12) x loan_tenor))
            limit = (numerator * term) / (1 + (ratePerMonth * term));
        }

        // Round down to the nearest million (e.g., 118.868.741 -> 118.000.000)
        // If you strictly wanted 115.000.000 from 118.xxx.xxx, that would require custom steps.
        // Assuming standard "round down to nearest million":
        const result = Math.floor(limit / 1000000) * 1000000;

        return result;
    },

    // f1. Analysis f logic
    updateRatesFromTerm() {
        const term = parseFloat(this.loanTerm) || 0;
        const defaultRate = (term < 3) ? 0.5 : 1;

        if (!this.isProvisionManual) {
            this.loanProvisionRate = defaultRate;
        }
        if (!this.isAdminManual) {
            this.loanAdminRate = defaultRate;
        }
    },
    calcDefaultProvision() {
        return (this.loanAmount * (this.loanProvisionRate / 100));
    },
    calcDefaultAdmin() {
        return (this.loanAmount * (this.loanAdminRate / 100));
    },
            get loanTotalRealizationCost() {
        return (parseFloat(this.loanProvisionAmount) || 0) +
            (parseFloat(this.loanAdminAmount) || 0) +
            (parseFloat(this.loanStampDuty) || 0) +
            (parseFloat(this.loanNotary) || 0) +
            (parseFloat(this.loanInsurance) || 0) +
            (parseFloat(this.loanOtherCost) || 0);
    },

            // f3. Installment Logic
            get monthlyInterest() {
        return Math.round((this.loanAmount * (this.interestRate / 100)) / 12);
    },
            get monthlyPrincipal() {
        // If Seasonal, principal is not paid monthly
        if (this.loanType === 'Pinjaman Musiman') {
            return 0;
        }
        const tenor = parseFloat(this.loanTerm) || 1;
        return Math.round(this.loanAmount / tenor);
    },
            get monthlyInstallment() {
        return Math.round(this.monthlyInterest + this.monthlyPrincipal);
    },

            // Recommended installment based on maxLoanLimit
            get recMonthlyInterest() {
        return Math.round((this.maxLoanLimit * (this.interestRate / 100)) / 12);
    },
            get recMonthlyPrincipal() {
        if (this.loanType === 'Pinjaman Musiman') {
            return 0;
        }
        const tenor = parseFloat(this.loanTerm) || 1;
        return Math.round(this.maxLoanLimit / tenor);
    },
            get recMonthlyInstallment() {
        return Math.round(this.recMonthlyInterest + this.recMonthlyPrincipal);
    },

            // g. Saldo Akhir Kas & Bank
            get endCashBankBefore() {
        // e - f1 + f2 - f3
        // Before: e_before - f1 + f2 - 0
        return this.endOpBalanceBefore - this.loanTotalRealizationCost + (parseFloat(this.loanAmount) || 0);
    },
            get endCashBankAfter() {
        // e - f1 + f2 - f3
        // After: e_after - 0 + 0 - f3
        return this.endOpBalanceAfter - this.monthlyInstallment;
    },

            get loanRemBalanceBefore() {
        return parseFloat(this.loanAmount) || 0;
    },
            get loanRemBalanceAfter() {
        return (parseFloat(this.loanAmount) || 0) - this.monthlyPrincipal; // Assuming simple reduction for 'After'
    },

            // Part 3 Totals & Helpers

            get totalLiabilitiesEquityBefore() {
        return (parseFloat(this.liabThirdPartyBefore) || 0) +
            (parseFloat(this.liabBPRBefore) || 0) +
            (parseFloat(this.liabOtherBefore) || 0) +
            (parseFloat(this.equityOwnBefore) || 0) +
            (parseFloat(this.profitCurrentBefore) || 0) +
            (parseFloat(this.profitPastBefore) || 0);
    },

            get totalLiabilitiesEquityAfter() {
        return (parseFloat(this.liabThirdPartyAfter) || 0) +
            (parseFloat(this.liabBPRAfter) || 0) +
            (parseFloat(this.liabOtherAfter) || 0) +
            (parseFloat(this.equityOwnAfter) || 0) +
            (parseFloat(this.profitCurrentAfter) || 0) +
            (parseFloat(this.profitPastAfter) || 0);
    },

            // New Neraca Computations
            get totalAktivaLancar() {
        return (parseFloat(this.openingCash) || 0) + (parseFloat(this.openingSavings) || 0) + (parseFloat(this.openingGiro) || 0);
    },
            get totalAssetNeraca() {
        return this.totalAktivaLancar + parseFloat(this.totalCustomAssets);
    },
            get totalKewajibanLancar() {
        return parseFloat(this.totalExternalLoanOutstanding) + (parseFloat(this.kewajibanLancar) || 0);
    },
            get modalUsaha() {
        const totalAssets = this.totalAssetNeraca;
        const totalOtherLiabilities = this.totalKewajibanLancar + (parseFloat(this.kewajibanJangkaPanjang) || 0) + parseFloat(this.labaBerjalan);
        return totalAssets - totalOtherLiabilities;
    },
            get totalKewajibanDanModal() {
        return this.totalKewajibanLancar + (parseFloat(this.kewajibanJangkaPanjang) || 0) + parseFloat(this.labaBerjalan) + this.modalUsaha;
    },



    // Part 4 Logic
    calculateInstallment(index) {
        const loan = this.loans[index];
        const originalAmount = parseFloat(loan.original_amount?.toString().replace(/\D/g, '')) || 0;
        const outstandingBalance = parseFloat(loan.outstanding_balance?.toString().replace(/\D/g, '')) || 0;
        const rate = parseFloat(loan.interest_rate) || 0;
        const term = parseFloat(loan.term_months) || 0;
        const method = loan.interest_method;

        if (term === 0) {
            loan.installment_amount = 0;
            return;
        }

        let installment = 0;

        // Monthly Rate Decimal
        const monthlyRate = (rate / 100) / 12;

        if (method === 'Flat') {
            // (Original / Term) + (Original * Rate / 12)
            const principal = originalAmount / term;
            const interest = originalAmount * monthlyRate;
            installment = principal + interest;
        } else if (method === 'Anuitas') {
            // PMT = P * r * (1 + r)^n / ((1 + r)^n - 1)
            if (monthlyRate === 0) {
                installment = originalAmount / term;
            } else {
                const numerator = originalAmount * monthlyRate * Math.pow(1 + monthlyRate, term);
                const denominator = Math.pow(1 + monthlyRate, term) - 1;
                installment = numerator / denominator;
            }
        } else if (method === 'Efektif') {
            // Sliding: Principal + Interest on Outstanding
            // Note: This changes every month. We usually calculate the INITIAL installment for analysis.
            // Principal = Original / Term
            // Interest = Outstanding * Rate / 12

            // If outstanding is provided, we use it. If not (new loan), we might assume outstanding = original.
            // The user prompt implies this is "History", so Outstanding is likely standard.
            const principalPortion = originalAmount / term;
            const interestPortion = outstandingBalance * monthlyRate;
            installment = principalPortion + interestPortion;
        } else if (method === 'Musiman') {
            // Seasonal: Usually interest only monthly, principal at harvest/end.
            installment = originalAmount * monthlyRate;
        }

        loan.installment_amount = this.formatNumber(Math.round(installment));
    },

    calculateLoanTenor(index) {
        const loan = this.loans[index];
        const realization = loan.realization_date;
        const maturity = loan.maturity_date;

        if (realization && maturity) {
            const start = new Date(realization);
            const end = new Date(maturity);

            // Calculate difference in months
            let months = (end.getFullYear() - start.getFullYear()) * 12;
            months -= start.getMonth();
            months += end.getMonth();

            // Adjust for partial months if needed, but standard is usually rough month diff
            // If day of month of end is less than start, maybe subtract?
            // Banking usually counts full months or days.
            // For simplicity and standard loan terms, simple month diff is often used.
            // Let's refine: if end day < start day, it might not be a full month allowed yet.
            // But usually loans are integer months.
            // Let's stick to the year/month diff first.
            // (Optional: Round to nearest or use day diff / 30)

            // Logic: Difference in months.
            // Example: Jan 1 to Jan 1 next year = 12 months.

            if (months > 0) {
                loan.term_months = months;
                // Recalculate installment with new term
                this.calculateInstallment(index);
            }
        }
    },

    formatLoanField(item, field) {
        const value = item[field];
        if (!value) return;
        const numericValue = value.toString().replace(/\D/g, '');
        item[field] = this.formatNumber(numericValue);
    },

    formatNumber(value) {
        if (value === null || value === undefined || value === '') return '';
        return new Intl.NumberFormat('id-ID').format(value);
    },

    formatCollateralValue(index, field, value) {
        const numericValue = value.replace(/\D/g, '');
        this.collaterals[index][field] = this.formatNumber(numericValue);
        // Auto-calculate bank_value when market_value changes
        if (field === 'market_value') {
            const col = this.collaterals[index];
            const bankVal = this.getBankValue(col);
            col.bank_value = this.formatNumber(bankVal);
        }
    },

    getBankValue(col) {
        const raw = parseFloat((col.market_value || '0').toString().replace(/\D/g, '')) || 0;
        if (col.type === 'certificate') {
            return Math.round(raw * 0.8);
        } else if (col.type === 'vehicle') {
            return Math.round(raw * 0.5);
        }
        return 0;
    },

            get totalCollateralBankValue() {
        return this.collaterals.reduce((sum, col) => {
            return sum + this.getBankValue(col);
        }, 0);
    },

    getCollateralImage(col, imgIdx) {
        // Returns filename if exists
        if (col.type === 'certificate') {
            return col['property_image_' + (imgIdx + 1)];
        } else if (col.type === 'vehicle') {
            return col['vehicle_image_' + (imgIdx + 1)];
        }
        return null;
    },

    updateOldLoanAmount(value) {
        const numericValue = value.replace(/\D/g, '');
        this.oldLoanAmount = numericValue;
        this.displayOldLoanAmount = this.formatNumber(numericValue);
    },

            get filteredCustomers() {
        let result = this.formattedCustomers;
        if (this.search !== '') {
            const lowerSearch = this.search.toLowerCase();
            result = result.filter(customer => {
                return customer.name.toLowerCase().includes(lowerSearch) ||
                    (customer.identity_number && customer.identity_number.includes(this.search)) ||
                    (customer.address && customer.address.toLowerCase().includes(lowerSearch));
            });
        }
        return result;
    },
            get filteredEconomicSectors() {
        if (this.searchEconomicSector === '') {
            return this.economicSectors;
        }
        const lower = this.searchEconomicSector.toLowerCase();
        return this.economicSectors.filter(item => {
            return item.name.toLowerCase().includes(lower) || item.code.toLowerCase().includes(lower);
        });
    },
            get paginatedCustomers() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredCustomers.slice(start, end);
    },
            get totalPages() {
        return Math.ceil(this.filteredCustomers.length / this.itemsPerPage);
    },
            get calculatedInstallment() {
        const principal = parseFloat(this.loanAmount) || 0;
        const termMonths = parseInt(this.loanTerm) || 0;
        const ratePerYear = parseFloat(this.interestRate) || 0;

        if (principal > 0 && termMonths > 0) {
            if (this.loanType === 'Pinjaman Musiman') {
                return Math.round((principal * (ratePerYear / 100)) / 12);
            }

            const totalInterest = principal * (ratePerYear / 100) * (termMonths / 12);
            const totalPayable = principal + totalInterest;
            return Math.round(totalPayable / termMonths);
        }
        return 0;
    },
            get calculatedOldInstallment() {
        const principal = parseFloat(this.oldLoanAmount) || 0;
        const termMonths = parseInt(this.oldLoanTerm) || 0;
        const ratePerYear = parseFloat(this.oldInterestRate) || 0;

        if (principal > 0 && termMonths > 0) {
            if (this.oldLoanType === 'Pinjaman Musiman') {
                return Math.round((principal * (ratePerYear / 100)) / 12);
            }

            const totalInterest = principal * (ratePerYear / 100) * (termMonths / 12);
            const totalPayable = principal + totalInterest;
            return Math.round(totalPayable / termMonths);
        }
        return 0;
    },
            get isAgeRisky() {
        if (!this.selectedCustomer || !this.selectedCustomer.dob) return false;
        const termMonths = parseFloat(this.loanTerm) || 0;
        if (termMonths <= 0) return false;

        const dob = new Date(this.selectedCustomer.dob);
        const today = new Date();

        // Calculate Maturity Date
        const maturityDate = new Date(today);
        maturityDate.setMonth(maturityDate.getMonth() + termMonths);

        // Calculate Age at Maturity
        let ageAtMaturity = maturityDate.getFullYear() - dob.getFullYear();
        const m = maturityDate.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && maturityDate.getDate() < dob.getDate())) {
            ageAtMaturity--;
        }

        return ageAtMaturity > 60;
    },

    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    },
    nextPage() {
        if (this.currentPage < this.totalPages) this.currentPage++;
    },
    prevPage() {
        if (this.currentPage > 1) this.currentPage--;
    },
    selectCustomer(customer) {
        this.selectedCustomer = customer;
        this.showModal = false;
    },

    // Collateral Logic
    addCollateral(data = null) {
        const defaultCol = {
            type: 'certificate',
            certificate_number: '',
            land_area: '',
            building_area: '',
            location_address: '',
            brand: '',
            model: '',
            year: '',
            color: '',
            police_number: '',
            chassis_number: '',
            engine_number: '',
            owner_name: '',
            owner_ktp: '',
            proof_type: '',
            proof_of_ownership: '',
            market_value: '',
            bank_value: '',
            latitude: '',
            longitude: '',
            village: '',
            district: '',
            regency: '',
            province: ''
        };

        // If data provided (from existing), merge it
        if (data) {
            // Map existing data to form structure if names likely match or need mapping
            // Assuming existing data keys match our form keys for simplicity
            // or we manually map them here.
            const newCol = { ...defaultCol, ...data };
            // Ensure type is lowercase
            newCol.type = newCol.type.toLowerCase();
            // Reset IDs if any, we want new entry
            delete newCol.id;
            delete newCol.evaluation_id;
            delete newCol.created_at;
            delete newCol.updated_at;

            this.collaterals.push(newCol);
        } else {
            this.collaterals.push(defaultCol);
        }
    },
    removeCollateral(index) {
        this.collaterals.splice(index, 1);
    },

            get availableCollaterals() {
        if (!this.selectedCustomer || !this.selectedCustomer.evaluations) return [];
        const allCols = [];
        this.selectedCustomer.evaluations.forEach(ev => {
            if (ev.collaterals) {
                ev.collaterals.forEach(col => {
                    allCols.push(col);
                });
            }
        });
        return allCols;
    },

            get filteredAvailableCollaterals() {
        let cols = this.availableCollaterals;

        // Filter Type
        if (this.collateralFilter !== 'all') {
            cols = cols.filter(c => c.type.toLowerCase() === this.collateralFilter);
        }

        // Search Text
        if (this.collateralSearch) {
            const lowerQ = this.collateralSearch.toLowerCase();
            cols = cols.filter(c => {
                return (c.owner_name && c.owner_name.toLowerCase().includes(lowerQ)) ||
                    (c.certificate_number && c.certificate_number.toLowerCase().includes(lowerQ)) ||
                    (c.police_number && c.police_number.toLowerCase().includes(lowerQ)) ||
                    (c.location_address && c.location_address.toLowerCase().includes(lowerQ));
            });
        }
        return cols;
    },

    selectExistingCollateral(item) {
        this.addCollateral(item);
        this.showCollateralModal = false;

        // Show success toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Agunan berhasil ditambahkan',
            showConfirmButton: false,
            timer: 3000
        });
    },

    // --- Collateral Map Modal Logic ---
    openCollateralMap(index) {
        this.activeCollateralIndex = index;
        this.showCollateralMapModal = true;

        // Reset map state if needed, but keep instance if possible?
        // Better to invalidate size after transition
        this.$nextTick(() => {
            this.initCollateralMapModal();
        });
    },

    initCollateralMapModal() {
        const mapContainer = document.getElementById('collateral-modal-map');
        if (!mapContainer) return;

        const col = this.collaterals[this.activeCollateralIndex];
        // Default center (Indonesia) or specific default
        let lat = -2.5489;
        let lng = 118.0149;
        let zoom = 5;

        // Use existing data if available
        if (col.latitude && col.longitude) {
            lat = parseFloat(col.latitude);
            lng = parseFloat(col.longitude);
            zoom = 16;
        } else {
            // Try to use Part 1 location as fallback if available?
            // Or just use default. Let's start with default or browser location if we want to be fancy later.
            // For now, default Indonesia or slightly zoomed out.
        }

        if (!this.collateralMap) {
            const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                crossOrigin: true
            });

            const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri'
            });

            this.collateralMap = L.map('collateral-modal-map', {
                center: [lat, lng],
                zoom: zoom,
                layers: [osm]
            });

            const baseMaps = {
                "OpenStreetMap": osm,
                "Google Streets": googleStreets,
                "Google Satellite": googleHybrid,
                "Esri Satellite": esriSatellite
            };

            L.control.layers(baseMaps).addTo(this.collateralMap);

            this.collateralMap.on('click', (e) => {
                this.updateCollateralMarker(e.latlng.lat, e.latlng.lng);
            });
        } else {
            this.collateralMap.invalidateSize();
            this.collateralMap.setView([lat, lng], zoom);
        }

        // Marker
        if (col.latitude && col.longitude) {
            this.updateCollateralMarker(lat, lng);
        } else {
            if (this.collateralMarker) {
                this.collateralMap.removeLayer(this.collateralMarker);
                this.collateralMarker = null;
            }
        }
    },

    updateCollateralMarker(lat, lng) {
        if (this.collateralMarker) {
            this.collateralMarker.setLatLng([lat, lng]);
        } else {
            this.collateralMarker = L.marker([lat, lng], { draggable: true }).addTo(this.collateralMap);

            // Drag event
            this.collateralMarker.on('dragend', (e) => {
                // Just update visual, actual save happens on button click
                const pos = e.target.getLatLng();
                // We could auto-fetch address here if we want to show it in modal?
                // For now, let's just allow moving.
            });
        }

        // Optional: Pan to marker
        // this.collateralMap.panTo([lat, lng]);
    },

            async getLocationForCollateral() {
        if (navigator.geolocation) {

            Swal.fire({
                title: 'Mendeteksi Lokasi...',
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    this.updateCollateralMarker(lat, lng);
                    this.collateralMap.setView([lat, lng], 16);
                    Swal.close();
                },
                (error) => {
                    Swal.fire('Error', 'Gagal mendeteksi lokasi.', 'error');
                }
            );
        } else {
            Swal.fire('Error', 'Browser tidak mendukung Geolocation.', 'error');
        }
    },

            async saveCollateralLocation() {
        if (!this.collateralMarker) {
            Swal.fire('Warning', 'Silakan pilih titik lokasi pada peta.', 'warning');
            return;
        }

        const latlng = this.collateralMarker.getLatLng();
        const lat = latlng.lat;
        const lng = latlng.lng;

        // Update data
        if (this.activeCollateralIndex !== null && this.collaterals[this.activeCollateralIndex]) {
            this.collaterals[this.activeCollateralIndex].latitude = lat.toFixed(8);
            this.collaterals[this.activeCollateralIndex].longitude = lng.toFixed(8);

            // Fetch Address using the global helper from create.blade.php
            if (window.fetchAddressForModal) {
                try {
                    const addr = await window.fetchAddressForModal(lat, lng, 'return_object');
                    if (addr) {
                        // Update properties
                        this.collaterals[this.activeCollateralIndex].village = addr.village;
                        this.collaterals[this.activeCollateralIndex].district = addr.district;
                        this.collaterals[this.activeCollateralIndex].regency = addr.regency;
                        this.collaterals[this.activeCollateralIndex].province = addr.province;

                        // Force Alpine reactivity for deep array changes
                        // By re-assigning the whole object
                        this.collaterals[this.activeCollateralIndex] = { ...this.collaterals[this.activeCollateralIndex] };
                    }
                } catch (e) {
                    console.error('Address fetch failed', e);
                }
            }
        }

        this.showCollateralMapModal = false;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Lokasi berhasil disimpan',
            showConfirmButton: false,
            timer: 1500
        });
    },

    confirmSubmit() {
        // Ensure document_checklist is appended to the form before submitting
        const form = document.getElementById('evaluation-form');
        if (form) {
            let docInput = form.querySelector('input[name="document_checklist"]');
            if (!docInput) {
                docInput = document.createElement('input');
                docInput.type = 'hidden';
                docInput.name = 'document_checklist';
                form.appendChild(docInput);
            }
            docInput.value = JSON.stringify(this.checkedDocuments);
            console.log("Submitting Checked Documents Data: ", docInput.value);
        }

        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: "Apakah Anda yakin data yang dimasukkan sudah benar? Data akan diproses untuk evaluasi.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#15803d', // green-700
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan & Proses',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Check if we need to capture map (Wirausaha only)
                if (this.entrepreneurshipStatus === 'Wirausaha') {
                    if (typeof window.captureMapAndSubmit === 'function') {
                        window.captureMapAndSubmit(form);
                    } else {
                        console.error('captureMapAndSubmit function not found');
                        form.submit();
                    }
                } else {
                    form.submit();
                }
            }
        });
    }
        }));
    });
</script>