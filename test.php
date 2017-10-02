<?php

require_once __DIR__ . '/vendor/autoload.php';

use \Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Projet {
    public $id;
    public $nom;
    public $date_debut;
    public $estArchive;

    public function __construct($id, $nom, DateTime $date_debut) {
        $this->id = $id;
        $this->nom = $nom;
        $this->date_debut = $date_debut;
        $this->estArchive = false;
    }

    public function archive() {
        $this->estArchive = true;
    }
    public function getDateDebut() {
        return $this->date_debut;
    }
}


final class Language extends ExpressionLanguage
{
    protected function registerFunctions()
    {
        // Registering our 'date' function
        $this->register('date', function ($date) {
            return sprintf('(new \DateTime(%s))', $date);
        }, function (array $values, $date) {
            return new \DateTime($date);
        });

        // Registering our 'date_modify' function
        $this->register('date_modify', function ($date, $modify) {
            return sprintf('%s->modify(%s)', $date, $modify);
        }, function (array $values, $date, $modify) {
            if (!$date instanceof \DateTime) {
                throw new \RuntimeException('date_modify() expects parameter 1 to be a Date');
            }
            return $date->modify($modify);
        });
    }
}

$language = new Language();
$projets = [];
$projets[] = new Projet(1, 'test', new DateTime('2017-09-01'));
$projets[] = new Projet(2, 'test', new DateTime('2017-11-01'));
$projets[] = new Projet(3, 'test', new DateTime('2018-02-01'));

foreach ($projets as $projet) {
    var_dump($language->evaluate(
        '
            date("now") < projet.getDateDebut() && date_modify(projet.getDateDebut(), "-3 months") < date("now")
            ',
        array(
            'projet' => $projet,
        )
    ));
}