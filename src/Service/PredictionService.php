<?php

// src/Service/PredictionService.php
namespace App\Service;

use DateTime;

class PredictionService
{
    public function predictSellOrConserve(DateTime $buyingDate, bool $isNew, int $fixCount): string
    {
        $currentDate = new DateTime();
        $age = $currentDate->diff($buyingDate)->y;


        if ($isNew) {
            if ($fixCount > 3) {
                return '😦It is better to Sell it🙁';
            } else {
                return 'Good😇, keep it up👍🏻';
            }
        } else {
            if ($age > 5 || $fixCount > 5) {
                return '😦It is better to Sell it🙁';
            } else {
                return 'Good😇,  conserve it ✌️';
            }
        }
    }
}