<?php

namespace Framework\Twig;

use DateTime;
use Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('datetime', [$this, 'datetime'])
        ];
    }

    /**
     * @param string|DateTime|null $dateTime
     * @return string
     * @throws Exception
     */
    public function datetime($dateTime): string
    {
        if (is_null($dateTime)) {
            $date = '';
        } else {
            $month = ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jui', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'];
            if (is_string($dateTime)) {
                $dateTime = new DateTime($dateTime);
            }
            $date = explode('|', $dateTime->setTimezone(new \DateTimeZone('Africa/Brazzaville'))->format('d|n|Y|H|:|i'), $dateTime->getTimestamp());
            $date = $date[0] . ' ' . $month[$date[1] - 1] . ' ' . $date[2] . ' ' . $date[3] . '' . $date[4] . '' . $date[5];
        }
        return $date;
    }
}
