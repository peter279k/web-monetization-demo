<?php

namespace App\Domain\Monetization\Service;

use App\Domain\Monetization\Repository\MonetizationRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;

/**
 * Service.
 */
final class MonetizationFetcher
{
    /**
     * @var MonetizationRepository
     */
    private $repository;

    /**
     * @var LoggerFactory
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param MonetizationRepository $repository The repository
     */
    public function __construct(MonetizationRepository $repository, LoggerFactory $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger->addFileHandler('monetization_creator.log')->createInstance('monetization_creator');
    }

    /**
     * Fetch the transaction history monetization amounts.
     *
     * @param string $paymentPointer The payment pointer
     *
     * @return array The transaction monetization amounts
     */
    public function fetchMonetizationAmount(string $paymentPointer): array
    {
        // Input validation
        $this->validateHistoryMonetizationAmount($paymentPointer);

        // Insert monetization amount
        $historyAmounts = $this->repository->fetchMonetizationTransactionHistory($paymentPointer);

        // Logging here: Web Monetization created successfully
        $this->logger->info(sprintf('History Web Monetization amount fetched successfully: %s', $paymentPointer));

        return $historyAmounts;
    }

    /**
     * Input validation
     *
     * @param string $paymentPointer payment pointer
     *
     * @throws ValidationException
     *
     * @return void
     */
    private function validateHistoryMonetizationAmount(string $paymentPointer): void
    {
        $errors = [];

        if ($this->validatePaymentPointer($paymentPointer) === false) {
            $errors['paymentPointer'] = 'paymentPointer should be correct format';
        }

        if ($errors) {
            throw new ValidationException('Please check your monetization payment pointer input', $errors);
        }
    }

    /**
     * Validate payment pointer
     *
     * @param string $paymentPointer
     */
    private function validatePaymentPointer(string $paymentPointer): bool
    {
        $matchedCount = preg_match('/(\$ilp).(\w+).(\w+)\/(\w+)/', $paymentPointer, $matched);

        if ($matchedCount === 0) {
            return false;
        }

        return $matched[0] === $paymentPointer;
    }
}
