<?php

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

class UserTest extends KernelTestCase
{

    public function getEntity($usernameTest, $email, $password)
    {
        $user = new User();
        return $user->setUsername($usernameTest)->setEmail($email)->setPassword($password);
    }
    public static function FakeUser(): array
    {
        return [
            'data set 1' => ['john', 'john@doe.fr', password_hash('secretValide', PASSWORD_BCRYPT),  0],
            'data set 2' => ['JANE', '1@1.fr', password_hash('secret', PASSWORD_BCRYPT), 0],
            'data set 3' => ['TotoDu07', 'TotoDu07@s.fr', password_hash('secretValide125sd!', PASSWORD_BCRYPT), 0],
            'data set 4' => ['', '@doe.fr', 123456, 3],
            'data set 5' => [1, '', 'p', 2],
            'data set 6' => ['#*#*', '@.fr', 'password123', 2],
            'data set 7' => [NAN, '@', '$2y$10$toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong!', 2],
            'data set 8' => ['<script>"Protection"</script>', '.fr', '', 3],
            'data set 9' => [null, '', '$2y$10$eW5IYzE2cm5IYzE2cm5IdUJlUnJlb3JsWGVmV1d1WnFnWUlXZXNh', 1],
            'data set 10' => ['TotoDu07', null, null, 1],
        ];
    }

    /**
     * @dataProvider FakeUser
     * @testDox('utilise $usernameTest, $email, $password et $errorsExpected')]
     */
    public function testValidatorUser($usernameTest, $email, $password, $errorsExpected): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $user = $this->getEntity($usernameTest, $email, $password);
        $errors = $container->get('validator')->validate($user);
        $messages = [];
        foreach ($errors as $error) {
            $messages = $error ? $error->getPropertyPath() . '=>' . $error->getMessage() : '';
        }
        $infoMessages = empty($messages) ? '' : $messages;
        $this->assertCount($errorsExpected, $errors, $infoMessages);
    }



    public static function FakeGoodUsernameProvider(): array
    {
        return [
            'data set 1' => ['john'],
            'data set 2' => ['JANE'],
            'data set 3' => ['TotoDu07'],
        ];
    }

    public static function FakeBadUsernameProvider(): array
    {
        return [
            'data set 1' => [''],
            'data set 2' => [1],
            'data set 3' => [0],
            'data set 4' => [NAN],
            'data set 5' => ['<script>"Protection"</script>'],
            'data set 6' => [null],
        ];
    }

    public function testUsername(User $user, string $usernameTest)
    {
        $this->assertEquals($usernameTest, $user->getUsername(), "Le nom d'utilisateur devrait être égal à celui défini");
        $this->assertNotEmpty($usernameTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        $this->assertIsString($usernameTest, "Cette variable devrait être une string");
        $this->assertGreaterThanOrEqual(1, strlen($usernameTest), "Cette variable devrait avoir min 1 caractère");
        $this->assertLessThanOrEqual(64, strlen($usernameTest), "Cette variable devrait avoir max 64 caractère");
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9_-]*$/',
            $usernameTest,
            "Cette variable ne doit contenir que des lettres, des chiffres, des tirets bas (_) et des tirets (-)."
        );
        // $existingUser = static::getContainer()->get('doctrine.orm.entity_manager')->findOneBy(['username' => $usernameTest]);

        // // Si $existingUser = Null c'est qu'il n'est pas dans la BDD
        // $this->assertNull($existingUser, "Le nom d'utilisateur '$usernameTest' devrait être unique");
    }

    /**
     * @dataProvider FakeGoodUsernameProvider
     * @testDox('utilise $username')]
     */
    public function testValideUsername($username)
    {
        $user = new User();
        $user->setUsername($username);
        $this->testUsername($user, $username);
    }

    /**
     * @dataProvider FakeBadUsernameProvider
     * @testDox('utilise $username')]
     */
    public function testInvalideUsername($username)
    {
        $user = new User();
        $user->setUsername($username);
        $this->testUsername($user, $username);
    }

    public static function FakeGoodUserMailProvider(): array
    {
        return [
            'data set 1' => ['john@doe.fr'],
            'data set 2' => ['1@1.fr'],
        ];
    }

    public static function FakeBadUserMailProvider(): array
    {
        return [
            'data set 1' => ['@doe.fr'],
            'data set 2' => ['a@.fr'],
            'data set 3' => ['@.fr'],
            'data set 4' => ['@'],
            'data set 5' => ['.fr'],
            'data set 6' => [''],
        ];
    }

    public function testEmail(User $user)
    {

        $this->assertNotEmpty($user->getEmail(), "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        $this->assertIsString($user->getEmail(), "Cette variable devrait être une string");
        $this->assertGreaterThan(1, strlen($user->getEmail()), "Cette variable devrait avoir min 1 caractère");
        $this->assertLessThanOrEqual(64, strlen($user->getEmail()), "Cette variable devrait avoir max 64 caractère");
        $this->assertMatchesRegularExpression('/^.+@.+\..+$/', $user->getEmail(), "L'email devrait être valide et non vide");
    }

    /**
     * @dataProvider FakeGoodUserMailProvider
     * @testDox('utilise $email')]
     */
    public function testValideEmail($email)
    {
        $user = new User();
        $user->setEmail($email);
        $this->testEmail($user);
    }
    /**
     * @dataProvider FakeBadUserMailProvider
     * @testDox('utilise $email')]
     */
    public function testInvalideEmail($email)
    {
        $user = new User();
        $user->setEmail($email);
        $this->testEmail($user);
    }

    public static function FakeGoodPasswordProvider(): array
    {
        return [
            'data set 2' => [123456789101112], //Un mot de passe numérique
            'data set 3' => ['123456789101112'], //Un mot de passe simple sans aucun hash
            'data set 4' => ['passwords123!'], //Un mot de passe simple sans aucun hash
            'data set 5' => ['$azdazd4453467qscazd!'], //Un faux hash trop long.
            'data set 6' => ['$2y$10$azedsd146a5z4d50'], //Un faux hash avec un préfixe dollar 
            // 'data set 7' => [], //Une chaîne vide
        ];
    }
    public static function FakeBadPasswordProvider(): array
    {
        return [
            'data set 1' => [''], //Une chaîne vide
            'data set 2' => ['p'], //Un mot de passe simple sans aucun hash
            'data set 3' => ['password123'], //Un mot de passe simple sans aucun hash
            'data set 4' => ['$2y$10$toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong!'], //Un faux hash trop long.
            'data set 5' => [], //Une chaîne vide
        ];
    }

    public function testPassword(User $user)
    {
        $this->assertNotEmpty($user->getPassword(), "Cette variable devrait avoir min:12 caractère jusqu'à max: 64");
        $this->assertIsString($user->getPassword(), "Cette variable devrait être une string");
        $this->assertGreaterThan(12, strlen($user->getPassword()), "Cette variable devrait avoir min 12 caractère");
        $this->assertLessThanOrEqual(180, strlen($user->getPassword()), "Cette variable devrait avoir max 180 caractère");
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
        $this->assertMatchesRegularExpression(
            '/^\$2[ayb]\$.{56}$/',
            $user->getPassword(),
            "Le mot de passe doit être un hash bcrypt valide."
        );
    }


    /**
     * @dataProvider FakeGoodPasswordProvider
     * @testDox('utilise $password')]
     */
    public function testValidePassword($password)
    {
        $user = new User();
        $user->setPassword($password);
        $this->testPassword($user);
    }
    /**
     * @dataProvider FakeBadPasswordProvider
     * @testDox('utilise $password')]
     */
    public function testInvalidePassword($password)
    {
        $user = new User();
        $user->setPassword($password);
        $this->testPassword($user);
    }

    public static function FakeGoodRoleProvider(): array
    {
        return [
            'data set 1' => [['ROLE_USER']],
            'data set 2' => [['ROLE_USER', 'ROLE_ADMIN']],
        ];
    }
    public static function FakeBadRoleProvider(): array
    {
        return [
            'data set 1' => [], //Une chaîne vide
            'data set 2' => [''], //Une chaîne vide
            'data set 3' => [[123456]], //Un role numérique
            'data set 4' => [['']], //Une chaîne vide
        ];
    }

    public function testRoles(User $user)
    {
        $this->assertNotEmpty($user->getRoles(), "Le rôle devrait être non vide");
        $this->assertIsArray($user->getRoles(), "Le/Les rôle(s) doivent être un tableau");
        // Vérifie que chaque rôle est une chaîne non vide
        foreach ($user->getRoles() as $role) {
            $this->assertIsString($role, "Chaque rôle doivent être une chaîne");
            $this->assertNotEmpty($role, "Chaque rôle ne doit pas être une chaîne vide");
        }
    }


    /**
     * @dataProvider FakeGoodRoleProvider
     * @testDox('utilise $role')]
     */
    public function testValideRoles($role)
    {
        $user = new User();
        $user->setRoles($role);
        $this->testRoles($user);
    }
    /**
     * @dataProvider FakeBadRoleProvider
     * @testDox('utilise $roles')]
     */
    public function testInvalideRoles($role)
    {
        $user = new User();
        $user->setRoles($role);
        $this->testRoles($user);
    }
    public static function FakeUsernameWithExpectedErrorsProvider(): array
    {
        return [
            'data set 1' => ['john', 0],
            'data set 2' => ['JANE', 0],
            'data set 3' => ['TotoDu07', 0],
            'data set 4' => ['#*#*', 2],
            'data set 5' => ['', 1],
            'data set 6' => [1, 1],
            'data set 7' => [0, 2],
            'data set 8' => [NAN, 1],
            'data set 9' => ['<script>"Protection"</script>', 1],
            'data set 10' => [null, 3],
        ];
    }

    /**
     * @dataProvider FakeUsernameWithExpectedErrorsProvider
     * @testDox('utilise $usernameTest et $expectedErrors')]
     */
    public function testUsernameWithExpectedErrors($usernameTest, $expectedErrors)
    {
        $actualErrors = 0;

        try {
            $this->assertNotEmpty($usernameTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        try {
            $this->assertIsString($usernameTest, "Cette variable devrait être une string");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        if (is_string($usernameTest)) {
            try {
                $this->assertLessThanOrEqual(64, strlen($usernameTest), "Cette variable devrait avoir max 64 caractère");
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                $this->assertMatchesRegularExpression(
                    '/^[a-zA-Z0-9_-]*$/',
                    $usernameTest,
                    "Cette variable ne doit contenir que des lettres, des chiffres, des tirets bas (_) et des tirets (-)."
                );
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
        }
        try {
            $this->assertNotEquals("#*#*", $usernameTest, "Cette variable devrait être une string");
        } catch (PHPUnit\Framework\AssertionFailedError $e) {
            $actualErrors++;
        }
        try {
            $this->assertNotNull($usernameTest, "ne peut être null");
        } catch (PHPUnit\Framework\AssertionFailedError $e) {
            $actualErrors++;
        }

        $this->assertEquals($expectedErrors, $actualErrors, "Le nombre d'erreurs n'est pas celui attendu.");
    }


    public static function FakeEmailWithExpectedErrorsProvider(): array
    {
        return [
            'data set 1' => ['john@doe.fr', 0],
            'data set 2' => ['1@1.fr', 0],
            'data set 3' => ['@doe.fr', 1],
            'data set 4' => ['a@.fr', 1],
            'data set 5' => ['@.fr', 1],
            'data set 6' => ['@', 2],
            'data set 7' => ['.fr', 1],
            'data set 8' => ['', 3],
            'data set 9' => [null, 3],
        ];
    }

    /**
     * @dataProvider FakeEmailWithExpectedErrorsProvider
     * @testDox('utilise $emailTest et $expectedErrors')]
     */
    public function testEmailWithExpectedErrors($emailTest, $expectedErrors)
    {
        $actualErrors = 0;

        try {
            $this->assertNotEmpty($emailTest, "Cette variable devrait avoir min:1 caractère jusqu'à max: 64");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        try {
            $this->assertIsString($emailTest, "Cette variable devrait être une string");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        if (is_string($emailTest)) {
            try {
                $this->assertGreaterThan(1, strlen($emailTest), "Cette variable devrait avoir min 1 caractère");
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                $this->assertLessThanOrEqual(64, strlen($emailTest), "Cette variable devrait avoir max 64 caractère");
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                $this->assertMatchesRegularExpression('/^.+@.+\..+$/', $emailTest, "L'email devrait être valide et non vide");
            } catch (PHPUnit\Framework\AssertionFailedError $e) {
                $actualErrors++;
            }
        }
        try {
            $this->assertNotNull($emailTest, "ne peut être null");
        } catch (PHPUnit\Framework\AssertionFailedError $e) {
            $actualErrors++;
        }

        $this->assertEquals($expectedErrors, $actualErrors, "Le nombre d'erreurs n'est pas celui attendu.");
    }

    public static function FakePasswordWithExpectedErrorsProvider(): array
    {
        return [
            'data set 1' => ['testValide', 0],
            'data set 2' => [123456, 1],
            'data set 3' => ['p', 2],
            'data set 4' => ['password123', 2],
            'data set 5' => ['$2y$10$toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong!', 2],
            'data set 6' => ['', 3],
            'data set 7' => ['$2y$10$eW5IYzE2cm5IYzE2cm5IdUJlUnJlb3JsWGVmV1d1WnFnWUlXZXNh', 1],
            'data set 8' => [null, 3],
        ];
    }

    /**
     * @dataProvider FakePasswordWithExpectedErrorsProvider
     * @testDox('utilise $passwordTest et $expectedErrors')]
     */
    public function testPasswordWithExpectedErrors($passwordTest, $expectedErrors)
    {
        $actualErrors = 0;
        if ('testValide' === $passwordTest) {
            $passwordTest = password_hash($passwordTest, PASSWORD_BCRYPT);
        }

        try {
            $this->assertNotEmpty($passwordTest, "Cette variable devrait avoir min:12 caractère jusqu'à max: 64");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        try {
            $this->assertIsString($passwordTest, "Cette variable devrait être une string");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        if (is_string($passwordTest)) {
            try {
                $this->assertGreaterThan(12, strlen($passwordTest), "Cette variable devrait avoir min 12 caractère");
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                $this->assertLessThanOrEqual(180, strlen($passwordTest), "Cette variable devrait avoir max 180 caractère");
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                $this->assertMatchesRegularExpression(
                    '/^\$2[ayb]\$.{56}$/',
                    $passwordTest,
                    "Le mot de passe doit être un hash bcrypt valide."
                );
            } catch (PHPUnit\Framework\AssertionFailedError $e) {
                $actualErrors++;
            }
        }
        try {
            $this->assertNotNull($passwordTest, "ne peut être null");
        } catch (PHPUnit\Framework\AssertionFailedError $e) {
            $actualErrors++;
        }

        $this->assertEquals($expectedErrors, $actualErrors, "Le nombre d'erreurs n'est pas celui attendu.");
    }

    public static function FakeRolesWithExpectedErrorsProvider(): array
    {
        return [
            'data set 1' => [['ROLE_USER'], 0],
            'data set 2' => [['ROLE_USER', 'ROLE_ADMIN'], 0],
            'data set 4' => ['', 2],
            'data set 5' => [[123456], 1], //Un role numérique
            'data set 6' => [[''], 1], //Un tableau avec une chaîne vide
            'data set 7' => [null, 3], //Un tableau avec une chaîne vide
        ];
    }

    /**
     * @dataProvider FakeRolesWithExpectedErrorsProvider
     * @testDox('utilise $rolesTest et $expectedErrors')]
     */
    public function testRolesWithExpectedErrors($rolesTest, $expectedErrors)
    {
        $actualErrors = 0;

        try {
            $this->assertNotEmpty($rolesTest, "Le rôle devrait être non vide");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        try {
            $this->assertIsArray($rolesTest, "Le/Les rôle(s) doivent être un tableau");
        } catch (PHPUnit\Framework\AssertionFailedError) {
            $actualErrors++;
        }
        if (is_array($rolesTest)) {
            try {
                foreach ($rolesTest as $role) {
                    $this->assertIsString($role, "Chaque rôle doivent être une chaîne");
                }
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
            try {
                foreach ($rolesTest as $role) {
                    $this->assertNotEmpty($role, "Chaque rôle ne doit pas être une chaîne vide");
                }
            } catch (PHPUnit\Framework\AssertionFailedError) {
                $actualErrors++;
            }
        }
        try {
            $this->assertNotNull($rolesTest, "ne peut être null");
        } catch (PHPUnit\Framework\AssertionFailedError $e) {
            $actualErrors++;
        }

        $this->assertEquals($expectedErrors, $actualErrors, "Le nombre d'erreurs n'est pas celui attendu.");
    }
}
