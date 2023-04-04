<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$controller = 'AmadeusNew';

preg_match('/<(Fare_MasterPricerTravelBoardSearch|Fare_InformativePricingWithoutPNR|MiniRule_GetFromRec|Security_SignOut|Air_SellFromRecommendation|PNR_AddMultiElements|FOP_CreateFormOfPayment|Fare_PricePNRWithBookingClass|Ticket_CreateTSTFromPricing|PNR_Retrieve|DocIssuance_IssueTicket)/m', request()->getContent(), $output_array);

if (empty($output_array[1])) {
    $action = null;
    $name = null;
    return;
}

switch ($output_array[1]) {
    case 'Fare_MasterPricerTravelBoardSearch':
        $action = 'fareMasterPricerTravelBoardSearch';
        break;
    case 'Fare_InformativePricingWithoutPNR':
        $action = 'fareInformativePricingWithoutPNR';
        break;
    case 'MiniRule_GetFromRec':
        $action = 'miniRuleGetFromRec';
        break;
    case 'Security_SignOut':
        $action = 'securitySignOut';
        break;
    case 'Air_SellFromRecommendation':
        $action = 'airSellFromRecommendation';
        break;
    case 'PNR_AddMultiElements':
        $action = 'PNRAddMultiElements';
        break;
    case 'FOP_CreateFormOfPayment':
        $action = 'createFormOfPayment';
        break;
    case 'Fare_PricePNRWithBookingClass':
        $action = 'farePricePNRWithBookingClass';
        break;
    case 'Ticket_CreateTSTFromPricing':
        $action = 'ticketCreateTSTFromPricing';
        break;
    case 'PNR_Retrieve':
        $action = 'PNRRetrieve';
        break;
    case 'DocIssuance_IssueTicket':
        $action = 'docIssuanceIssueTicket';
        break;
}
