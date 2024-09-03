<?php

declare(strict_types=1);

namespace App\UI\Quote\Builder\Response;

use App\Quote\Application\DTO\ProposalQuotesDTO;
use App\Quote\Application\DTO\QuoteDTO;
use App\UI\Quote\DTO\Response\ShopifyQuoteResponse;

final class QuoteResponseBuilder
{
    /**
     * @param QuoteDTO[] $quoteDTOs
     * @return ShopifyQuoteResponse[]
     */
    public function build(ProposalQuotesDTO $proposalQuotesDTO): array
    {
        $proposals = [];

        if (!is_null($proposalQuotesDTO->getFastestToday())) {
            $proposals[] = new ShopifyQuoteResponse(
                'Need it for tonight',
                'tonight#' . $proposalQuotesDTO->getFastestToday()->getId(),
                $proposalQuotesDTO->getFastestToday()->getPrice(),
                'Fastest delivery option. Estimated today delivery time: ' . $proposalQuotesDTO->getFastestToday()->getDeliveryDateTo()->format('H:i'),
                $proposalQuotesDTO->getFastestToday()->getCurrency()
            );
        }

        if (!is_null($proposalQuotesDTO->getToday())) {
            $proposals[] = new ShopifyQuoteResponse(
                'Need it for today',
                'today#' . $proposalQuotesDTO->getToday()->getId(),
                $proposalQuotesDTO->getToday()->getPrice(),
                'Optimal delivery option. Estimated today delivery time: ' . $proposalQuotesDTO->getToday()->getDeliveryDateTo()->format('H:i'),
                $proposalQuotesDTO->getToday()->getCurrency()
            );
        }

        if (!is_null($proposalQuotesDTO->getLatest())) {
            $proposals[] = new ShopifyQuoteResponse(
                'Need it for tomorrow',
                'tomorrow#' . $proposalQuotesDTO->getLatest()->getId(),
                $proposalQuotesDTO->getLatest()->getPrice(),
                'Normal delivery option. Estimated delivery time: ' . $proposalQuotesDTO->getLatest()->getDeliveryDateTo()->format('Y/m/d H:i'),
                $proposalQuotesDTO->getLatest()->getCurrency()
            );
        }

        return $proposals;
    }
}
