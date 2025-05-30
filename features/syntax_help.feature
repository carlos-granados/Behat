Feature: Syntax helpers
  In order to get syntax help
  As a feature writer
  I need to be able to print supported definitions and Gherkin keywords

  Scenario: Print story syntax
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php class FeatureContext implements Behat\Behat\Context\Context {}
      """
    When I run "behat --no-colors --story-syntax"
    Then the output should contain:
      """
      [Business Need|Ability|Feature]: Internal operations
        In order to stay secret
        As a secret organization
        We need to be able to erase past agents' memory

        Background:
          [Given|*] there is agent A
          [And|*] there is agent B

        [Scenario|Example]: Erasing agent memory
          [Given|*] there is agent J
          [And|*] there is agent K
          [When|*] I erase agent K's memory
          [Then|*] there should be agent J
          [But|*] there should not be agent K

        [Scenario Template|Scenario Outline]: Erasing other agents' memory
          [Given|*] there is agent <agent1>
          [And|*] there is agent <agent2>
          [When|*] I erase agent <agent2>'s memory
          [Then|*] there should be agent <agent1>
          [But|*] there should not be agent <agent2>

          [Scenarios|Examples]:
            | agent1 | agent2 |
            | D      | M      |
      """

  Scenario: Print story syntax in native language
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php class FeatureContext implements Behat\Behat\Context\Context {}
      """
    When I run "behat --no-colors --story-syntax --lang el"
    Then the output should contain:
      """
      # language: el
      [Δυνατότητα|Λειτουργία]: Internal operations
        In order to stay secret
        As a secret organization
        We need to be able to erase past agents' memory

        Υπόβαθρο:
          [Δεδομένου|*] there is agent A
          [Και|*] there is agent B

        [Παράδειγμα|Σενάριο]: Erasing agent memory
          [Δεδομένου|*] there is agent J
          [Και|*] there is agent K
          [Όταν|*] I erase agent K's memory
          [Τότε|*] there should be agent J
          [Αλλά|*] there should not be agent K

        [Περίγραμμα Σεναρίου|Περιγραφή Σεναρίου]: Erasing other agents' memory
          [Δεδομένου|*] there is agent <agent1>
          [Και|*] there is agent <agent2>
          [Όταν|*] I erase agent <agent2>'s memory
          [Τότε|*] there should be agent <agent1>
          [Αλλά|*] there should not be agent <agent2>

          [Παραδείγματα|Σενάρια]:
            | agent1 | agent2 |
            | D      | M      |
      """

  Scenario: Print available definitions
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context,
          Behat\Behat\Exception\PendingException;
      use Behat\Step\Given;
      use Behat\Step\Then;
      use Behat\Step\When;

      class FeatureContext implements Context
      {
          #[Given('/^(?:I|We) have (\d+) apples?$/')]
          public function iHaveApples($count) {
              throw new PendingException();
          }

          #[When('/^(?:I|We) ate (\d+) apples?$/')]
          public function iAteApples($count) {
              throw new PendingException();
          }

          #[When('/^(?:I|We) found (\d+) apples?$/')]
          public function iFoundApples($count) {
              throw new PendingException();
          }

          #[Then('/^(?:I|We) should have (\d+) apples$/')]
          public function iShouldHaveApples($count) {
              throw new PendingException();
          }
      }
      """
    When I run "behat --no-colors -dl"
    Then the output should contain:
      """
      default | Given /^(?:I|We) have (\d+) apples?$/
      default | When /^(?:I|We) ate (\d+) apples?$/
      default | When /^(?:I|We) found (\d+) apples?$/
      default | Then /^(?:I|We) should have (\d+) apples$/
      """

  Scenario: Print available definitions in native language
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context,
          Behat\Behat\Exception\PendingException,
          Behat\Behat\Context\TranslatableContext;
      use Behat\Step\Given;
      use Behat\Step\Then;
      use Behat\Step\When;

      class FeatureContext implements TranslatableContext
      {
          #[Given('/^I have (\d+) apples?$/')]
          public function iHaveApples($count) {
              throw new PendingException();
          }

          #[When('/^I ate (\d+) apples?$/')]
          public function iAteApples($count) {
              throw new PendingException();
          }

          #[When('/^I found (\d+) apples?$/')]
          public function iFoundApples($count) {
              throw new PendingException();
          }

          #[Then('/^I should have (\d+) apples$/')]
          public function iShouldHaveApples($count) {
              throw new PendingException();
          }

          public static function getTranslationResources() {
              return array(__DIR__ . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . 'ru.xliff');
          }
      }
      """
    And a file named "features/bootstrap/i18n/ru.xliff" with:
      """
      <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file original="global" source-language="en" target-language="ru" datatype="plaintext">
          <header />
          <body>
            <trans-unit id="i-have-apples">
              <source>/^I have (\d+) apples?$/</source>
              <target>/^у меня (\d+) яблоко?$/</target>
            </trans-unit>
            <trans-unit id="i-found">
              <source>/^I found (\d+) apples?$/</source>
              <target>/^Я нашел (\d+) яблоко?$/</target>
            </trans-unit>
          </body>
        </file>
      </xliff>
      """
    When I run "behat --no-colors -dl --lang=ru"
    Then the output should contain:
      """
      default | Допустим /^у меня (\d+) яблоко?$/
      default | Когда /^I ate (\d+) apples?$/
      default | Когда /^Я нашел (\d+) яблоко?$/
      default | Затем /^I should have (\d+) apples$/
      """

  Scenario: Print extended definitions info
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context,
          Behat\Behat\Exception\PendingException;
      use Behat\Step\Given;
      use Behat\Step\Then;
      use Behat\Step\When;

      class FeatureContext implements Context
      {
          #[Given('/^I have (\d+) apples?$/')]
          public function iHaveApples($count) {
              throw new PendingException();
          }

          /**
           * Eating apples
           *
           * More details on eating apples, and a list:
           * - one
           * - two
           * --
           * Internal note not showing in help
           */
          #[When('/^I ate (\d+) apples?$/')]
          public function iAteApples($count) {
              throw new PendingException();
          }

          #[When('/^I found (\d+) apples?$/')]
          public function iFoundApples($count) {
              throw new PendingException();
          }

          #[Then('/^I should have (\d+) apples$/')]
          public function iShouldHaveApples($count) {
              throw new PendingException();
          }
      }
      """
    When I run "behat --no-colors -di"
    Then the output should contain:
      """
      default | [Given|*] /^I have (\d+) apples?$/
              | at `FeatureContext::iHaveApples()`

      default | [When|*] /^I ate (\d+) apples?$/
              | Eating apples
              | More details on eating apples, and a list:
              | - one
              | - two
              | at `FeatureContext::iAteApples()`

      default | [When|*] /^I found (\d+) apples?$/
              | at `FeatureContext::iFoundApples()`

      default | [Then|*] /^I should have (\d+) apples$/
              | at `FeatureContext::iShouldHaveApples()`
      """

  Scenario: Print extended definitions info with file name and line numbers
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context,
          Behat\Behat\Exception\PendingException;
      use Behat\Step\Given;
      use Behat\Step\Then;
      use Behat\Step\When;

      class FeatureContext implements Context
      {
          #[Given('/^I have (\d+) apples?$/')]
          public function iHaveApples($count) {
              throw new PendingException();
          }

          /**
           * Eating apples
           *
           * More details on eating apples, and a list:
           * - one
           * - two
           * --
           * Internal note not showing in help
           */
          #[When('/^I ate (\d+) apples?$/')]
          public function iAteApples($count) {
              throw new PendingException();
          }

          #[When('/^I found (\d+) apples?$/')]
          public function iFoundApples($count) {
              throw new PendingException();
          }

          #[Then('/^I should have (\d+) apples$/')]
          public function iShouldHaveApples($count) {
              throw new PendingException();
          }
      }
      """
    When I run "behat --no-colors -di -v"
    Then the output should contain:
      """
      default | [Given|*] /^I have (\d+) apples?$/
              | at `FeatureContext::iHaveApples()`
              | on `%%WORKING_DIR%%features%%DS%%bootstrap%%DS%%FeatureContext.php[12:14]`

      default | [When|*] /^I ate (\d+) apples?$/
              | Eating apples
              | More details on eating apples, and a list:
              | - one
              | - two
              | at `FeatureContext::iAteApples()`
              | on `%%WORKING_DIR%%features%%DS%%bootstrap%%DS%%FeatureContext.php[26:28]`

      default | [When|*] /^I found (\d+) apples?$/
              | at `FeatureContext::iFoundApples()`
              | on `%%WORKING_DIR%%features%%DS%%bootstrap%%DS%%FeatureContext.php[31:33]`

      default | [Then|*] /^I should have (\d+) apples$/
              | at `FeatureContext::iShouldHaveApples()`
              | on `%%WORKING_DIR%%features%%DS%%bootstrap%%DS%%FeatureContext.php[36:38]`
      """

  Scenario: Search definition
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context,
          Behat\Behat\Exception\PendingException,
          Behat\Behat\Context\TranslatableContext;
      use Behat\Step\Given;
      use Behat\Step\Then;
      use Behat\Step\When;

      class FeatureContext implements TranslatableContext
      {
          #[Given('/^I have (\d+) apples?$/')]
          public function iHaveApples($count) {
              throw new PendingException();
          }

          #[When('/^I ate (\d+) apples?$/')]
          public function iAteApples($count) {
              throw new PendingException();
          }

          #[When('/^I found (\d+) apples?$/')]
          public function iFoundApples($count) {
              throw new PendingException();
          }

          #[Then('/^I should have (\d+) apples$/')]
          public function iShouldHaveApples($count) {
              throw new PendingException();
          }

          public static function getTranslationResources() {
              return array(__DIR__ . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . 'ru.xliff');
          }
      }
      """
    And a file named "features/bootstrap/i18n/ru.xliff" with:
      """
      <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file original="global" source-language="en" target-language="ru" datatype="plaintext">
          <header />
          <body>
            <trans-unit id="i-have-apples">
              <source>/^I have (\d+) apples?$/</source>
              <target>/^у меня (\d+) яблоко?$/</target>
            </trans-unit>
            <trans-unit id="i-found">
              <source>/^I found (\d+) apples?$/</source>
              <target>/^Я нашел (\d+) яблоко?$/</target>
            </trans-unit>
          </body>
        </file>
      </xliff>
      """
    When I run "behat --no-colors --lang=ru -d 'нашел'"
    Then the output should contain:
      """
      default | [Когда|Если|*] /^Я нашел (\d+) яблоко?$/
              | at `FeatureContext::iFoundApples()`
      """
