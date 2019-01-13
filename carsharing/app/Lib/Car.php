<?php

namespace App\Lib;

use Geokit\LatLng;

class Car
{
    /**
     * @var string
     */
    private $company;
    /**
     * @var LatLng
     */
    private $coordinates;
    /**
     * @var int
     */
    private $oil;
    /**
     * @var int
     */
    private $price;

    /**
     * CarsMap constructor.
     * @param string $id
     * @param string $company
     * @param LatLng $coordinates
     * @param int $oil
     * @param int $price
     */
    public function __construct(string $id, string $company, LatLng $coordinates, int $oil, int $price)
    {
        $this->id = $id;
        $this->company = $company;
        $this->coordinates = $coordinates;
        $this->oil = $oil;
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'oil' => $this->oil,
            'price' => $this->price,
            'coordinates' => [$this->coordinates->getLatitude(), $this->coordinates->getLongitude()]
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return LatLng
     */
    public function getCoordinates(): LatLng
    {
        return $this->coordinates;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return int
     */
    public function getOil(): int
    {
        return $this->oil;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    public function getYandexStyle(): string
    {
        switch ($this->company) {
            case Delimobil::NAME:
                $style = 'islands#darkOrangeIcon';
                break;
            case Youdrive::NAME:
                $style = 'islands#darkGreenIcon';
                break;
            default:
                $style = 'islands#blueIcon';
        }

        return $style;
    }
}
