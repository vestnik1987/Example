<?php

namespace App\...;

class ExampleTest
{
    private TimeService | MockInterface $timeService;

    private DefaultPayStrategy $service;

    public function _before(FunctionalTester $I)
    {
        $this->timeService = Mockery::mock(TimeService::class);
        $I->setService(TimeService::class, $this->timeService);

        $this->service = $I->grabService(DefaultPayStrategy::class);

        $I->load([
            ContractExample::TABLE => [
                ContractExample::get([
                    'c_id' => 10,
                ]),
            ],
            ProjectExample::TABLE => [
                ProjectExample::get([
                    'p_id' => 5,
                    'p_contract' => 10,
                ]),
            ],
            ConversionExample::TABLE => [],
        ]);
    }

    protected function setInvoicePaidSuccessProvider()
    {
        return [
            [
                'comment' => 'wallet is taken from invoices',
                'wallet' => 'test',
                'transaction' => uniqid(),
            ],
            [
                'comment' => 'not transaction',
                'wallet' => uniqid(),
                'transaction' => null,
            ],
        ];
    }

    /**
     * @dataProvider setInvoicePaidSuccessProvider
     */
    public function setInvoicePaidSuccessTest(FunctionalTester $I, Example $example)
    {
        $I->comment($example['comment']);

        $invoiceId = 1;
        $projectId = 1;
        $systemId = 1;
        $invoiceAccountId = 2;
        $projectAmount = 495.50;
        $date = '2018-01-01 00:00:00';
        $newPaidTime = new DateTimeImmutable($date);

        $this->timeService
            ->expects('create')
            ->andReturn($newPaidTime);

        $this->service->setInvoicePaid(
            $invoiceId,
            $systemId,
            $projectId,
            $example['wallet'],
            $example['transaction']
        );

        $I->seeInDatabase(
            ProjectBalanceExample::TABLE,
            [
                'p_income' => $projectAmount,
                'p_spends' => 0,
                'p_balance' => $projectAmount,
                'p_paygate_gain' => 0,
                'p_id' => $projectId,
                'p_aid' => $invoiceAccountId,
            ]
        );
    }
}
