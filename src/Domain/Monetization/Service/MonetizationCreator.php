<?php

namespace App\Domain\Monetization\Service;

use App\Domain\Monetization\Repository\MonetizationRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;

/**
 * Service.
 */
final class MonetizationCreator
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
     * Create a new web monetization amount.
     *
     * @param array $data The form data
     *
     * @return int The new amount ID
     */
    public function createMonetizationAmount(array $data): int
    {
        // Input validation
        $this->validateNewMonetizationAmount($data);

        // Insert monetization amount
        $amountId = $this->repository->insertMonetization($data);

        // Logging here: Web Monetization created successfully
        $this->logger->info(sprintf('Monetization amount created successfully: %s', $amountId));

        return $amountId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     *
     * @return void
     */
    private function validateNewMonetizationAmount(array $data): void
    {
        $errors = [];

        if (empty($data['amount'])) {
            $errors['amount'] = 'Input required';
        }

        if (empty($data['assetCode'])) {
            $errors['assetCode'] = 'Input required';
        }

        if (empty($data['assetScale'])) {
            $errors['assetScale'] = 'Input required';
        }

        if (empty($data['paymentPointer'])) {
            $errors['paymentPointer'] = 'Input required';
        }

        if (empty($data['requestId'])) {
            $errors['requestId'] = 'Input required';
        }

        if (is_numeric($data['amount']) === false) {
            $errors['amount'] = 'amount should be numeric value';
        }

        if (is_int($data['assetScale']) === false && $data['assetScale'] < 0) {
            $errors['assetScale'] = 'assetScale should be positive integer or zero value';
        }

        if ($this->validatePaymentPointer($data['paymentPointer']) === false) {
            $errors['paymentPointer'] = 'paymentPointer should be correct format';
        }

        if ($this->validateRequestId($data['requestId']) === false) {
            $errors['requestId'] = 'requestId should be UUIDv4 format';
        }

        if ($errors) {
            throw new ValidationException('Please check your monetization amount input', $errors);
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

    /**
     * Validate requestId
     *
     * @param string $requestId
     */
    private function validateRequestId(string $requestId): bool
    {
        $matchedCount = preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $requestId);

        return $matchedCount === 1;
    }
}
