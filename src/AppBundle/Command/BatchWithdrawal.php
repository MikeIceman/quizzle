<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 04.10.2018
	 * Time: 0:24
	 */

	namespace AppBundle\Command;

	use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use AppBundle\Entity\Operation;
	use AppBundle\Service\Banking;
	use Doctrine\ORM\EntityManager;

	class BatchWithdrawal extends ContainerAwareCommand
	{
		protected function configure()
		{
			$this
				->setName('withdrawal:batch')
				->setDescription('Approve withdrawal requests in batch')
				->addArgument(
					'limit',
					InputArgument::REQUIRED,
					'How many withdrawal requests you want to send?'
				)
			;
		}

		protected function execute(InputInterface $input, OutputInterface $output)
		{
			$limit = $input->getArgument('limit');
			$logger = $this->getContainer()->get('logger');
			$banking = $this->getContainer()->get(Banking::class);

			$logger->info("Executing batch withdrawal for $limit records");

			$output->writeln("Executing batch withdrawal for $limit records:");

			$em = $repository = $this->getContainer()->get('doctrine')->getManager();
			$repository = $repository = $this->getContainer()->get('doctrine')->getRepository(Operation::class);

			$operations = $repository->findBy(
				[
					'type' => 'withdrawal',
					'status' => 'pending'
				],
				[
					'id' => 'ASC'
				],
				$limit
			);
			foreach($operations as $operation)
			{
				$output->writeln("Username: ".$operation->getUser()->getUsername());
				$output->writeln("Operation ID: ".$operation->getId());
				$output->writeln("Amount: ".$operation->getAmount());
				$output->writeln("Sending funds via Paypal...");
				try
				{
					$response = $banking->sendWithdrawalRequest($operation);
					$output->writeln("Operation completed successfully.");
					$output->writeln("Batch ID: ".$response->getBatchHeader()->getPayoutBatchId());
					$output->writeln("Payment state: ".$response->getBatchHeader()->getBatchStatus());

					$operation->setTxnId($response->getBatchHeader()->getPayoutBatchId());
					$operation->setStatus('complete');
					$operation->setDateUpdated(new \DateTime());
					$operation->setUpdatedBy(null);
					$em->persist($operation);
					$em->flush();
					$output->writeln("Withdrawal request #".$operation->getId()." marked as COMPLETE");
				}
				catch (\Exception $ex)
				{
					$output->writeln($ex->getMessage());
				}
				$output->writeln("------=============------");
			}

		}
	}