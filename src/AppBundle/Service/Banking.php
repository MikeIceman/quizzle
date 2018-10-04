<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 03.10.2018
	 * Time: 23:11
	 */

	namespace AppBundle\Service;

	use Psr\Log\LoggerInterface;
	use AppBundle\Entity\User;
	use AppBundle\Entity\Operation;
	use PayPal\Api\Payout;
	use PayPal\Auth\OAuthTokenCredential;
	use PayPal\Rest\ApiContext;
	use PayPal\Api\PayoutSenderBatchHeader;
	use PayPal\Api\PayoutItem;
	use PayPal\Api\Currency;
	use Symfony\Component\CssSelector\Exception\InternalErrorException;

	class Banking
	{
		private $logger;

		public function __construct(LoggerInterface $logger)
		{
			$this->logger = $logger;
		}

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
				array(
					'mode' => 'sandbox',
					'log.LogEnabled' => true,
					'log.FileName' => __DIR__.'/../Log/PayPal.log',
					'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
					'cache.enabled' => false,
				)
			);

			$payouts = new Payout();
			$senderBatchHeader = new PayoutSenderBatchHeader();

			$senderBatchHeader->setSenderBatchId(uniqid())
				->setEmailSubject("Your funds withdrawal from Quizzle.tk");

			$senderItem = new PayoutItem();
			$senderItem->setRecipientType('Email')
				->setNote('Thanks you for your gaming expirience!')
				->setReceiver($operation->getUser()->getEmail())
				->setSenderItemId("item_1" . uniqid())
				->setAmount(new Currency('{
                        "value":'.$operation->getAmount().',
                        "currency":"USD"
                    }'));

			$payouts->setSenderBatchHeader($senderBatchHeader)
				->addItem($senderItem);

			// For Sample Purposes Only.
			$request = clone $payouts;

			// ### Create Payout
			try {
				$output = $payouts->create(null, $apiContext);
			} catch (Exception $ex) {
				$this->logger->error("Failed to Created Batch Payout: ".$ex->getMessage());
				throw new InternalErrorException("Failed to Created Batch Payout: ".$ex->getMessage());
			}
			$this->logger->info("Created Batch Payout: ".$operation->getUser()->getEmail()." : ".$operation->getAmount()." USD");
			return $output;
		}
	}