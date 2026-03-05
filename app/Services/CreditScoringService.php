<?php

namespace App\Services;

use App\Models\Evaluation;

class CreditScoringService
{
    /**
     * Calculate all scores for an evaluation based on its raw data
     *
     * @param Evaluation|array $evaluation
     * @return array
     */
    public function calculateScores($evaluation): array
    {
        // Handle both Model instance and array (from request)
        $data = is_array($evaluation) ? $evaluation : $evaluation->toArray();

        // Status Label Helper
        $statusLabel = function($score100) {
            if ($score100 >= 80) return 'Baik';
            if ($score100 >= 60) return 'Baik';
            if ($score100 >= 40) return 'Cukup';
            if ($score100 >= 20) return 'Kurang';
            return 'Buruk';
        };

        // 1. Character (30% weight)
        $charBureau = $data['char_credit_bureau'] ?? 0;
        $charInfo = $data['char_info_consistency'] ?? 0;
        $charRel = $data['char_relationship'] ?? 0;
        $charStab = $data['char_stability'] ?? 0;
        $charRep = $data['char_reputation'] ?? 0;
        
        $charTotal100 = ($charBureau / 5 * 25) + ($charInfo / 4 * 20) + ($charRel / 2 * 10) + ($charStab / 4 * 20) + ($charRep / 5 * 25);
        $charNilai = round($charTotal100 / 100 * 5, 1);
        $charSkala = round($charNilai / 5 * 30, 2);

        // 2. Capacity (20% weight)
        $capRpc = $data['cap_rpc'] ?? 0;
        $capLama = $data['cap_lama_usaha'] ?? 0;
        $capUsia = $data['cap_usia'] ?? 0;
        $capPeng = $data['cap_pengelolaan'] ?? 0;
        
        $capTotal100 = ($capRpc / 5 * 40) + ($capLama / 5 * 20) + ($capUsia / 5 * 20) + ($capPeng / 5 * 20);
        $capNilai = round($capTotal100 / 100 * 5, 1);
        $capSkala = round($capNilai / 5 * 20, 2);

        // 3. Capital (20% weight)
        $capitalDar = $data['capital_dar'] ?? 0;
        $capitalDer = $data['capital_der'] ?? 0;
        
        $capitalTotal100 = ($capitalDar / 5 * 40) + ($capitalDer / 5 * 60);
        $capitalNilai = round($capitalTotal100 / 100 * 5, 1);
        $capitalSkala = round($capitalNilai / 5 * 20, 2);

        // 4. Condition (10% weight)
        $condLokasi = $data['cond_lokasi'] ?? 0;
        $condProfit = $data['cond_profit'] ?? 0;
        $condDscr = $data['cond_dscr'] ?? 0;
        
        $condTotal100 = ($condLokasi / 5 * 20) + ($condProfit / 5 * 20) + ($condDscr / 5 * 60);
        $condNilai = round($condTotal100 / 100 * 5, 1);
        $condSkala = round($condNilai / 5 * 10, 2);

        // 5. Collateral (20% weight)
        $colKep = $data['col_kepemilikan'] ?? 0;
        $colPer = $data['col_peruntukan'] ?? 0;
        $colJalan = $data['col_lebar_jalan'] ?? 0;
        $colCov = $data['col_coverage'] ?? 0;
        $colMark = $data['col_marketable'] ?? 0;
        
        $colTotal100 = ($colKep / 5 * 20) + ($colPer / 5 * 10) + ($colJalan / 5 * 20) + ($colCov / 5 * 30) + ($colMark / 5 * 20);
        $colNilai = round($colTotal100 / 100 * 5, 1);
        $colSkala = round($colNilai / 5 * 20, 2);

        // Final combined score
        $totalSkala = $charSkala + $capSkala + $capitalSkala + $condSkala + $colSkala;
        $finalScore = round($totalSkala / 100 * 5, 2);

        // Kelayakan Eligibility
        if ($finalScore >= 4.61) $kelayakan = 'Sangat Layak';
        elseif ($finalScore >= 3.6) $kelayakan = 'Layak';
        elseif ($finalScore >= 2.81) $kelayakan = 'Cukup Layak';
        elseif ($finalScore >= 1.81) $kelayakan = 'Kurang Layak';
        else $kelayakan = 'Tidak Layak';

        return [
            'character' => [
                'total100' => $charTotal100,
                'nilai' => $charNilai,
                'skala' => $charSkala,
                'status' => $statusLabel($charTotal100)
            ],
            'capacity' => [
                'total100' => $capTotal100,
                'nilai' => $capNilai,
                'skala' => $capSkala,
                'status' => $statusLabel($capTotal100)
            ],
            'capital' => [
                'total100' => $capitalTotal100,
                'nilai' => $capitalNilai,
                'skala' => $capitalSkala,
                'status' => $statusLabel($capitalTotal100)
            ],
            'condition' => [
                'total100' => $condTotal100,
                'nilai' => $condNilai,
                'skala' => $condSkala,
                'status' => $statusLabel($condTotal100)
            ],
            'collateral' => [
                'total100' => $colTotal100,
                'nilai' => $colNilai,
                'skala' => $colSkala,
                'status' => $statusLabel($colTotal100)
            ],
            'final_score' => $finalScore,
            'kelayakan' => $kelayakan
        ];
    }
}
