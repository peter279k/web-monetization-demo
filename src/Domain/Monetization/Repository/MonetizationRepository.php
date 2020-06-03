<?php

namespace App\Domain\Monetization\Repository;

use PDO;
use App\Domain\Monetization\Data\MonetizationData;

/**
 * Repository.
 */
class MonetizationRepository
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Insert web monetization amount row.
     *
     * @param array $monetizationDetail The Web Monetization detail
     *
     * @return int The new ID
     */
    public function insertMonetization(array $monetizationDetail): int
    {
        $row = [
            'amount' => $monetizationDetail['amount'],
            'asset_code' => $monetizationDetail['assetCode'],
            'asset_scale' => $monetizationDetail['assetScale'],
            'payment_pointer' => $monetizationDetail['paymentPointer'],
            'request_id' => $monetizationDetail['requestId'],
            'created_date_time' => $monetizationDetail['created_date_time'],
        ];

        $sql = "INSERT INTO monetization_amounts SET
                amount=:amount,
                asset_code=:asset_code,
                asset_scale=:asset_scale,
                payment_pointer=:payment_pointer,
                request_id=:request_id,
                created_date_time=:created_date_time";

        $this->connection->beginTransaction();

        $this->connection->prepare($sql)->execute($row);

        $this->connection->commit();

        return $this->connection->lastInsertId();
    }

    /**
     * Fetch web monetization of created date time range.
     *
     * @return array
     */
    private function fetchCreatedDateTimeRange()
    {
        $sql = "SELECT
                max(created_date_time) as max_created_date_time,
                min(created_date_time) as min_created_date_time
                FROM monetization_amounts;";

        return $this->connection->query($sql)->fetchAll();
    }

    /**
     * Fetch web monetization transaction history result.
     *
     * @param string $monetizationPaymentPointer The Web Monetization payment pointer
     *
     * @return array The Web Monetization Transaction History Amounts
     */
    public function fetchMonetizationTransactionHistory(string $monetizationPaymentPointer): array
    {
        $fetchedResult = [];

        $row = [
            'payment_pointer' => $monetizationPaymentPointer,
        ];

        $sql = "SELECT
                amount,
                asset_code,
                asset_scale
                FROM monetization_amounts WHERE
                payment_pointer=:payment_pointer;";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($row);
        $results = (array)$stmt->fetchAll();

        $createdDateTimeRange = $this->fetchCreatedDateTimeRange();

        if (count($results) === 0) {
            return $fetchedResult;
        }

        $monetizationData = new MonetizationData();
        $fetchedResult['max_created_date_time'] = $createdDateTimeRange[0]['max_created_date_time'];
        $fetchedResult['min_created_date_time'] = $createdDateTimeRange[0]['min_created_date_time'];
        $fetchedResult['history_amount_data'] = [];

        foreach ($results as $result) {
            $monetizationData->amount = $result['amount'];
            $monetizationData->assetCode = $result['asset_code'];
            $monetizationData->assetScale = $result['asset_scale'];

            $fetchedResult['history_amount_data'][] = $monetizationData;
        }

        return $fetchedResult;
    }
}
