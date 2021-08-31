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
     * @param bool $time
     * @return string
     * @throws Exception
     */
    public function datetime($dateTime, bool $time = false, bool $months = false): string
    {
        if (is_null($dateTime)) {
            $date = '';
        } else {
            $month = ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jui', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'];
            if ($months) {
                $month = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            }
            if (is_string($dateTime)) {
                $dateTime = new DateTime($dateTime);
            }
            $datePart = explode('|', $dateTime->setTimezone(new \DateTimeZone('Africa/Brazzaville'))->format('d|n|Y|H|:|i'), $dateTime->getTimestamp());
            $date = $datePart[0] . ' ' . $month[$datePart[1] - 1] . ' ' . $datePart[2];
            if ($time) {
                $date .= ' ' . $datePart[3] . '' . $datePart[4] . '' . $datePart[5];
            }
        }
        return $date;
    }
}
