<?php

declare(strict_types=1);

namespace App\Quote\Application\Calculator;

use App\Quote\Application\Calculator\Provider\QuoteProviderInterface;
use App\Quote\Domain\DTO\AddressDTO;
use App\Quote\Domain\DTO\ProposalQuotesDTO;
use App\Quote\Domain\DTO\QuoteDTO;

// this will be strategy in future
final class QuoteCalculator
{
    private QuoteProviderInterface $quoteProvider;

    public function __construct(QuoteProviderInterface $quoteProvider)
    {
        $this->quoteProvider = $quoteProvider;
    }

    public function calculate(AddressDTO $addressDTO): ProposalQuotesDTO
    {
        $quotes = $this->quoteProvider->provide($addressDTO);

        if (empty($quotes)) {
            return ProposalQuotesDTO::ofNoAvailableRates();
        }

        $this->sortQuotes($quotes);

        return new ProposalQuotesDTO($this->getTodayFastest($quotes), $this->getToday($quotes), $this->getLatest($quotes));
    }

    private function sortQuotes(array &$quotes): void
    {
        usort($quotes, function (QuoteDTO $a, QuoteDTO $b) {
            return $a->getDeliveryDateTo()->getTimestamp() - $b->getDeliveryDateTo()->getTimestamp();
        });
    }

    /** @param QuoteDTO[] $quotes */
    private function getTodayFastest(array $quotes): ?QuoteDTO
    {
        if (!isset($quotes[0])) {
            return null;
        }

        $today = new \DateTimeImmutable();
        $firstQuote = $quotes[0];

        if ($firstQuote->getDeliveryDateTo()->format('Ymd') == $today->format('Ymd')) {
            return $firstQuote;
        }

        return null;
    }

    /** @param QuoteDTO[] $quotes */
    private function getToday(array $quotes): ?QuoteDTO
    {
        $today = new \DateTimeImmutable();
        $proposalQuote = null;
        $cheapestOption = PHP_INT_MAX;

        foreach ($quotes as $quote) {
            if ($quote->getDeliveryDateTo()->format('Ymd') == $today->format('Ymd') && $quote->getPrice() < $cheapestOption) {
                $proposalQuote = $quote;
                $cheapestOption = $quote->getPrice();
            }
        }

        return $proposalQuote;
    }

    /** @param QuoteDTO[] $quotes */
    private function getLatest(array $quotes): ?QuoteDTO
    {
        $today = new \DateTimeImmutable();

        foreach ($quotes as $quote) {
            if ($quote->getDeliveryDateTo()->format('Ymd') != $today->format('Ymd')) {
                return $quote;
            }
        }

        return null;
    }
}
