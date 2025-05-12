<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use App\Entity\Property;
use App\Entity\PropertyType;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $adminUser = new User();
        $adminUser->setEmail('admin@trocmontoit.com');
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('User');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword(
            $this->passwordHasher->hashPassword(
                $adminUser,
                'admin123'
            )
        );
        $manager->persist($adminUser);

        // Create regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'password'
            )
        );
        $manager->persist($user);

        // Create property types
        $propertyTypes = [];
        $typesData = [
            'Apartment' => 'Modern living spaces in residential buildings',
            'House' => 'Standalone residential buildings',
            'Chalet' => 'Wooden houses in mountain areas',
            'Villa' => 'Luxury homes with private grounds',
            'Boat' => 'Floating accommodations on water',
            'Yurt' => 'Circular tent-like structures',
            'Cabin' => 'Small wooden houses in natural settings',
            'Igloo' => 'Dome-shaped snow houses',
            'Tent' => 'Portable canvas shelters',
            'Car' => 'Vehicles converted for accommodation'
        ];

        foreach ($typesData as $name => $description) {
            $type = new PropertyType();
            $type->setName($name);
            $type->setDescription($description);
            $manager->persist($type);
            $propertyTypes[$name] = $type;
        }

        // Create equipment
        $equipmentData = [
            ['Wi-Fi Connection', 'High-speed internet access', 'fa-wifi'],
            ['Air Conditioning', 'Climate control for cooling', 'fa-snowflake'],
            ['Heating', 'Climate control for warming', 'fa-fire'],
            ['Washing Machine', 'For laundry needs', 'fa-tshirt'],
            ['Dryer', 'For drying clothes', 'fa-wind'],
            ['Television', 'Entertainment system', 'fa-tv'],
            ['Iron', 'For pressing clothes', 'fa-iron'],
            ['Nintendo Switch', 'Gaming console', 'fa-gamepad'],
            ['PS5', 'Gaming console', 'fa-gamepad'],
            ['Terrace', 'Outdoor space', 'fa-umbrella-beach'],
            ['Balcony', 'Elevated outdoor space', 'fa-door-open'],
            ['Swimming Pool', 'For swimming and relaxation', 'fa-swimming-pool'],
            ['Garden', 'Outdoor green space', 'fa-leaf']
        ];

        $equipments = [];
        foreach ($equipmentData as $data) {
            $equipment = new Equipment();
            $equipment->setName($data[0]);
            $equipment->setDescription($data[1]);
            $equipment->setIcon($data[2]);
            $manager->persist($equipment);
            $equipments[] = $equipment;
        }

        // Create services
        $servicesData = [
            ['Airport Transfer', 'Transportation to/from airport', 'fa-plane'],
            ['Breakfast', 'Morning meal service', 'fa-coffee'],
            ['Cleaning Service', 'Property cleaning during stay', 'fa-broom'],
            ['Car Rental', 'Vehicle rental service', 'fa-car'],
            ['Guided Tours', 'Local sightseeing with guide', 'fa-map-marked'],
            ['Cooking Classes', 'Learn local cuisine', 'fa-utensils'],
            ['Leisure Activities', 'Organized recreational activities', 'fa-hiking']
        ];

        $services = [];
        foreach ($servicesData as $data) {
            $service = new Service();
            $service->setName($data[0]);
            $service->setDescription($data[1]);
            $service->setIcon($data[2]);
            $manager->persist($service);
            $services[] = $service;
        }

        // Create properties
        $propertiesData = [
            [
                'Luxury Beachfront Villa',
                'Stunning villa with direct beach access, spacious rooms, and luxury amenities.',
                350.00,
                'Nice',
                '123 Beachfront Avenue',
                'Villa'
            ],
            [
                'Cozy Mountain Chalet',
                'Beautiful chalet nestled in the Alps with breathtaking views and a fireplace.',
                220.00,
                'Chamonix',
                '45 Mountain Pass',
                'Chalet'
            ],
            [
                'Modern City Apartment',
                'Stylish apartment in the heart of the city with modern furnishings and great views.',
                180.00,
                'Paris',
                '78 Boulevard Saint-Germain',
                'Apartment'
            ],
            [
                'Rustic Countryside House',
                'Peaceful house surrounded by nature, perfect for a quiet getaway.',
                150.00,
                'Provence',
                '12 Country Lane',
                'House'
            ],
            [
                'Canal Boat Experience',
                'Unique stay on a renovated traditional canal boat with all modern conveniences.',
                200.00,
                'Amsterdam',
                'Herengracht Canal',
                'Boat'
            ],
            [
                'Authentic Yurt Retreat',
                'Experience traditional nomadic living in a comfortable yurt with modern amenities.',
                120.00,
                'Dordogne',
                '56 Rural Route',
                'Yurt'
            ]
        ];

        foreach ($propertiesData as $i => $data) {
            $property = new Property();
            $property->setName($data[0]);
            $property->setDescription($data[1]);
            $property->setPricePerNight($data[2]);
            $property->setCity($data[3]);
            $property->setAddress($data[4]);
            $property->setPropertyType($propertyTypes[$data[5]]);
            
            // Add random equipment
            $numEquipment = rand(3, 8);
            shuffle($equipments);
            for ($j = 0; $j < $numEquipment; $j++) {
                $property->addEquipment($equipments[$j]);
            }
            
            // Add random services
            $numServices = rand(2, 5);
            shuffle($services);
            for ($j = 0; $j < $numServices; $j++) {
                $property->addService($services[$j]);
            }
            
            $manager->persist($property);
            
            // Add some as favorites for the user
            if ($i % 2 === 0) {
                $user->addFavorite($property);
            }
        }

        $manager->flush();
    }
}