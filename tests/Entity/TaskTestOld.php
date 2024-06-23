<?php

use App\Entity\Task;
use PHPUnit\Framework\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;

use function PHPUnit\Framework\throwException;

class TaskTest extends KernelTestCase
{

    public function getEntity($dateTest, $titleTest, $contentTest, $isDone)
    {
        $task = new Task();
        return $task->setCreatedAt($dateTest)->setTitle($titleTest)->setContent($contentTest)->setIsDone($isDone);
    }

    public static function FakeTask(): array
    {
        return [
            'data set 1' => [new DateTime(), 'Le chat', 'Penser à gratouiller le chat', true, 0],
            'data set 2' => [new DateTime('2023-06-01 14:36:01'), 'repas', 'penser a faire le repas', false, 0],
            'data set 3' => [new \DateTime('2024-06-01 14:36:01'), 'Coder', 'Apprendre a coder en PHP', true, 0],
            'data set 4' => ['e', '', '', 123456, 4],
            'data set 5' => [1, 1, 0, 'zdok', 1],
            'data set 6' => [0, '#*#*', '<script>"Protection"</script>', NAN, 1],
            'data set 7' => ['<script>"Protection"</script>', NAN, null, '', 2],
            'data set 8' => [null, '<script>"Protection"</>', '.fr', null, 3],
            'data set 9' => ['2022-12-25 14:36:01', null, '', '$2y$10$eW5IYzE2cm5IYzE2cm5IdUJlUnJlb3JsWGVmV1d1WnFnWUlXZXNh', 1],
            'data set 10' => ['date', 'TotoDu07', null, null, 1],
        ];
    }

    /**
     * @dataProvider FakeTask
     * @testDox('utilise $dateTest, $titleTest, $contentTest, $isDone et $errorsExpected')]
     */
    public function testValidatorTask($dateTest, $titleTest, $contentTest, $isDone, $errorsExpected): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $task = $this->getEntity($dateTest, $titleTest, $contentTest, $isDone, $errorsExpected);
        $errors = $container->get('validator')->validate($task);
        $messages = [];
        foreach ($errors as $error) {
            $messages = $error ? $error->getPropertyPath() . '=>' . $error->getMessage() : '';
        }
        $infoMessages = empty($messages) ? '' : $messages;
        $this->assertCount($errorsExpected, $errors, $infoMessages);
    }

    public static function FakeGoodDateTimeInterfaceProvider(): array
    {
        return [
            'data set 1' => ['2022-12-25 14:36:01'],
            'data set 2' => ['2023-06-01 14:36:01'],
            'data set 3' => ['2025-01-31 14:36:01'],
        ];
    }
    public static function FakeBadDateTimeInterfaceProvider(): array
    {
        return [
            'data set 1' => ['e'],
            'data set 2' => [1],
            'data set 3' => [0],
            'data set 4' => ['<script>"Protection"</script>'],
            'data set 5' => [null],
            'data set 6' => ['date'],
        ];
    }

    public function testDate(Task $task, DateTime $createdAtTest)
    {
        $this->assertEquals($createdAtTest, $task->getCreatedAt(), "La date de la tache devrait être égal à celui défini");
        $this->assertNotEmpty($createdAtTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        $this->assertInstanceOf(DateTime::class, $createdAtTest, 'la variable doit retourner une instance de DateTimeInterface');
    }

    /**
     * @dataProvider FakeGoodDateTimeInterfaceProvider
     * @testDox('utilise $dateTest')]
     */
    public function testValideDate($dateTest)
    {
        $task = new Task();
        $dateTimeTest = DateTime::createFromFormat('Y-m-d H:i:s', $dateTest);
        $task->setCreatedAt($dateTimeTest);
        $this->testDate($task, $dateTimeTest);
    }

    /**
     * @dataProvider FakeBadDateTimeInterfaceProvider
     * @testDox('utilise $dateTest')]
     */
    public function testInvalideDateTest($dateTest)
    {
        $task = new Task();
        $dateTimeTest = DateTime::createFromFormat('Y-m-d H:i:s', $dateTest);
        $task->setCreatedAt($dateTimeTest);
        $this->testDate($task, $dateTimeTest);
    }


    public static function FakeGoodTitleProvider(): array
    {
        return [
            'data set 1' => ['Le chat'],
            'data set 2' => ['Repas'],
            'data set 3' => ['Coder']
        ];
    }
    public static function FakeBadTitleProvider(): array
    {
        return [
            'data set 1' => [''],
            'data set 2' => [0],
            'data set 4' => ['<script>"Protection"</script>'],
            'data set 5' => [null],
        ];
    }


    public function testTitle(Task $task, string $titleTest)
    {
        $this->assertEquals($titleTest, $task->getTitle(), "Le titre devrait être égal à celui défini");
        $this->assertNotEmpty($titleTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        $this->assertIsString($titleTest, "Cette variable devrait être une string");
        $this->assertGreaterThanOrEqual(1, strlen($titleTest), "Cette variable devrait avoir min 1 caractère");
        $this->assertLessThanOrEqual(180, strlen($titleTest), "Cette variable devrait avoir max 180 caractère");
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9 _-]*$/',
            $titleTest,
            "Cette variable ne doit contenir que des lettres, des chiffres, des tirets bas (_), des espaces et des tirets (-)."
        );
    }

    /**
     * @dataProvider FakeGoodTitleProvider
     * @testDox('utilise $titleTest')]
     */
    public function testValideTitle($titleTest)
    {
        $task = new Task();
        $task->setTitle($titleTest);
        $this->testTitle($task, $titleTest);
    }

    /**
     * @dataProvider FakeBadTitleProvider
     * @testDox('utilise $titleTest')]
     */
    public function testInvalideTitle($titleTest)
    {
        $task = new Task();
        $task->setTitle($titleTest);
        $this->testTitle($task, $titleTest);
    }
    public static function FakeGoodContentProvider(): array
    {
        return [
            'data set 1' => ['Penser à gratouiller le chat'],
            'data set 2' => ['Faire a manger'],
            'data set 3' => ['Apprendre les test en PHP']
        ];
    }
    public static function FakeBadContentProvider(): array
    {
        return [
            'data set 1' => [''],
            'data set 2' => [0],
            'data set 4' => ['<script>"Protection"</script>'],
            'data set 5' => [null],
        ];
    }


    public function testContent(Task $task, string $contentTest)
    {
        $this->assertEquals($contentTest, $task->getContent(), "Le contenu devrait être égal à celui défini");
        $this->assertNotEmpty($contentTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        $this->assertIsString($contentTest, "Cette variable devrait être une string");
        $this->assertGreaterThanOrEqual(1, strlen($contentTest), "Cette variable devrait avoir min 1 caractère");
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9 _\-À-ÖØ-öø-ÿ]*$/u',
            $contentTest,
            "Cette variable ne doit contenir que des lettres, des chiffres, des tirets bas (_), des espaces et des tirets (-)."
        );
    }

    /**
     * @dataProvider FakeGoodContentProvider
     * @testDox('utilise $contentTest')
     */
    public function testValideContent($contentTest)
    {
        $task = new Task();
        $task->setContent($contentTest);
        $this->testContent($task, $contentTest);
    }

    /**
     * @dataProvider FakeBadContentProvider
     * @testDox('utilise $contentTest')
     */
    public function testInvalideContent($contentTest)
    {
        $task = new Task();
        $task->setContent($contentTest);
        $this->testContent($task, $contentTest);
    }

    public static function FakeGoodIsDoneProvider(): array
    {
        return [
            'data set 1' => [true],
            'data set 2' => [false],
            'data set 3' => [true]
        ];
    }

    //PHP évalue les valeurs suivantes à false: false, 0, 0.0, vide string (“”), “0”, NULL, un tableau vide; les autres valeurs sont true.
    public static function FakeBadIsDoneProvider(): array
    {
        return [
            'data set 1' => [''],
            'data set 2' => [null],
            'data set 3' => ['zdok'],
            'data set 4' => [200],
        ];
    }

    public function testIsDone(Task $task, $isDoneTest)

    {

        if (is_bool($isDoneTest)) {
            $this->assertIsNotString($task->getIsDone(), 'la variable devrait être une boolean');
            $this->assertIsNotNumeric($task->getIsDone(), 'la variable devrait être une boolean');
            $this->assertIsBool($task->getIsDone(), 'la variable devrait être une boolean');
            if ($isDoneTest === false) {
                $this->assertSame(false, $task->getIsDone());
                $this->assertTrue(($task->getIsDone() === false), 'la variable devrait être false');
            } elseif ($isDoneTest === true) {
                $this->assertSame(true, $task->getIsDone());
                $this->assertTrue(($task->getIsDone() === true), 'la variable devrait être true');
            }
        } else {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage("La valeur de 'isDone' doit être un booléen.");
        }

        // if ($isDoneTest === true) {
        //     $this->assertNotEmpty($isDoneTest, "cette variable ne devrait pas être vide");
        //     $this->assertNotNull($isDoneTest, "cette variable ne devrait pas être null");
        //     $this->assertIsNotString($isDoneTest, "cette variable ne devrait pas être une string");
        //     $this->assertIsBool($isDoneTest, "cette variable devrait être une boolean");
        //     $this->assertFalse(!is_bool($isDoneTest), "cette variable ne devrait pas être rara");
        // }
        // $this->assertEquals($isDoneTest, $task->getIsDone(), "Le contenu devrait être égal à celui défini");
        // $this->assertOnl
        // if (!is_bool($isDoneTest)) {
        //     throw new \InvalidArgumentException("La valeur doit être un booléen.");
        // }
        // $this->assertNotNull($isDoneTest, "Cette variable devrait être un booléen (vrai ou faux)");
        // $this->assertIsNotString($isDoneTest, "Cette variable devrait être un booléen (vrai ou faux)");
        // $this->assertIsBool($isDoneTest, "Cette variable devrait être un booléen (vrai ou faux)");
        // $this->assertSame($isDoneTest, $task->getIsDone(), "Le contenu devrait être égal à celui défini");
    }

    /**
     * @dataProvider FakeGoodIsDoneProvider
     * @testdox utilise $isDoneTest pour les cas invalides
     */
    public function testValideIsDone($isDoneTest)
    {
        $task = new Task();
        $task->setIsDone($isDoneTest);
        $this->testIsDone($task, $isDoneTest);
    }

    /**
     * @dataProvider FakeBadIsDoneProvider
     * @testdox utilise $isDoneTest pour les cas invalides
     */
    public function testInvalideIsDone($isDoneTest)
    {
        $task = new Task();
        $task->setIsDone($isDoneTest);
        $this->testIsDone($task, $isDoneTest);
    }
}
