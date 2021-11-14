<?php

namespace Framework\Twig;

use DateTime;
use DateTimeZone;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateTimeTwigExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ago', [$this, 'ago'])
        ];
    }

    public function ago(DateTime $date): string
    {
        $today = (new DateTime('now'))->setTimezone(new DateTimeZone('Africa/Brazzaville'));
        $dataDiff = $today->diff($date);
        switch ($dataDiff) {
            case $dataDiff->y !== 0:
                return $this->textToOutput($dataDiff->y, 'ans');
            case $dataDiff->m !== 0:
                return $this->textToOutput($dataDiff->m, 'mois');
            case $dataDiff->d !== 0:
                return $this->textToOutput($dataDiff->d, 'jours');
            case $dataDiff->h !== 0:
                return $this->textToOutput($dataDiff->h, 'heures');
            case $dataDiff->i !== 0:
                return $this->textToOutput($dataDiff->i, 'minutes');
            default:
                return "à l'instant";
        }
    }

    private function textToOutput(int $diff, string $period): string
    {
        $pd = $period !== "mois" ? $diff === 1 ? substr($period, 0, -1) : $period : $period;
        return "il y a $diff $pd";
    }
}
