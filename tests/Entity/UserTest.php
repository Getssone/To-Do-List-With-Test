<?php

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

class UserTest extends KernelTestCase
{

    // private $entityManager;

    // public function __construct(EntityManagerInterface $entityManager)
    // {
    //     $this->entityManager = $entityManager;
    //     parent::__construct();
    // }

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
        // $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $usernameTest]);
        // // Si $existingUser = Null c'est qu'il n'est pas dans la BDD
        // $this->assertNull($existingUser, "Le nom d'utilisateur '$usernameTest' devrait être unique");
    }


    /**
     * @dataProvider FakeGoodUsernameProvider
     * @testDox('utilise $username')]
     */
    public function testValidUsername($username)
    {
        $user = new User();
        $user->setUsername($username);
        $this->testUsername($user, $username);
    }
    /**
     * @dataProvider FakeBadUsernameProvider
     * @testDox('utilise $username')]
     */
    public function testInvalidUsername($username)
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
    public function testValidEmail($email)
    {
        $user = new User();
        $user->setEmail($email);
        $this->testEmail($user);
    }
    /**
     * @dataProvider FakeBadUserMailProvider
     * @testDox('utilise $email')]
     */
    public function testInvalidEmail($email)
    {
        $user = new User();
        $user->setEmail($email);
        $this->testEmail($user);
    }

    public static function FakePasswordProvider(): array
    {
        return [
            'data set 1' => [''], //Une chaîne vide
            'data set 2' => [123456], //Un mot de passe numérique
            'data set 3' => ['p'], //Un mot de passe simple sans aucun hash
            'data set 4' => ['password123'], //Un mot de passe simple sans aucun hash
            'data set 5' => ['$2y$10$toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong!'], //Un faux hash trop long.
            'data set 6' => ['$2y$10$toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong'], //Un faux hash avec un préfixe dollar 
        ];
    }

    public function testPassword(User $user)
    {
        $this->assertNotEmpty($user->getPassword(), "Cette variable devrait avoir min:12 caractère jusqu'à max: 64");
        $this->assertIsString($user->getPassword(), "Cette variable devrait être une string");
        $this->assertGreaterThan(12, strlen($user->getPassword()), "Cette variable devrait avoir min 12 caractère");
        $this->assertLessThanOrEqual(180, strlen($user->getPassword()), "Cette variable devrait avoir max 180 caractère");
        $this->assertMatchesRegularExpression(
            '/^\$2[ayb]\$.{56}$/',
            $user->getPassword(),
            "Le mot de passe doit être un hash bcrypt valide."
        );
    }


    /**
     * @dataProvider FakePasswordProvider
     * @testDox('utilise $password')]
     */
    public function testValidPassword($password)
    {
        $user = new User();
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $this->testPassword($user);
    }
    /**
     * @dataProvider FakePasswordProvider
     * @testDox('utilise $password')]
     */
    public function testInvalidPassword($password)
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
    public function testValidRoles($role)
    {
        $user = new User();
        $user->setRoles($role);
        $this->testRoles($user);
    }
    /**
     * @dataProvider FakeBadRoleProvider
     * @testDox('utilise $roles')]
     */
    public function testInvalidRoles($role)
    {
        $user = new User();
        $user->setRoles($role);
        $this->testRoles($user);
    }
}
