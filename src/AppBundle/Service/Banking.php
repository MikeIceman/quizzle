<?php

namespace AppBundle\Service;

use Exception;
use PayPal\Api\PayoutBatch;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Operation;
use PayPal\Api\Payout;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\PayoutItem;
use PayPal\Api\Currency;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

/**
 * Class Banking
 *
 * @package AppBundle\Service
 */
class Banking
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Banking constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Operation $operation
     *
     * @return PayoutBatch
     * @throws InternalErrorException
     */
    public function sendWithdrawalRequest(Operation $operation)
    {
        $this->logger->info("Sending new withdrawal request for user {$operation->getUser()->getUsername()}");

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS',     // ClientID
                'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL'      // ClientSecret
            )
        );

        $apiContext->setConfig(
            [
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => __DIR__ . '/../../../logs/PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
            ]
        );

        $payouts = new Payout();
        $senderBatchHeader = new PayoutSenderBatchHeader();

        $senderBatchHeader->setSenderBatchId(uniqid('', true))
            ->setEmailSubject("Your funds withdrawal from Quizzle.tk");

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote('Thanks you for your gaming expirience!')
            ->setReceiver($operation->getUser()->getEmail())
            ->setSenderItemId("item_1" . uniqid('', true))
            ->setAmount(
                new Currency('{"value":' . $operation->getAmount() . ', "currency":"USD"}')
            );

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        // ### Create Payout
        try {
            $output = $payouts->create(null, $apiContext);
        } catch (Exception $ex) {
            $this->logger->error("Failed to Created Batch Payout: " . $ex->getMessage());
            throw new InternalErrorException("Failed to Created Batch Payout: " . $ex->getMessage());
        }
        $this->logger->info(
            "Created Batch Payout: " . $operation->getUser()->getEmail() . " : " . $operation->getAmount() . " USD"
        );
        return $output;
    }
}
