<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Tests\Fixtures\Assert;
use Behat\Transformation\Transform;

class WholeTableAttributesContext implements Context
{
    private array $data;

    #[Transform('table:*')]
    public function transformTable(TableNode $table): array
    {
        return $table->getHash();
    }

    #[Given('data:')]
    public function givenData(array $data): void
    {
        $this->data = $data;
    }

    #[Then('the :field should be :value')]
    public function theFieldShouldBe(string $field, string $value): void
    {
        Assert::assertSame($value, $this->data[0][$field]);
    }
}
