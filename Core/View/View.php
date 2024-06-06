<?php

namespace Core\View;

use App\Controller\AuthController;

class View
{
    //on doit definir le chemin absolut vers le dossier views
    public const PATH_VIEW = PATH_ROOT . 'views' . DS;
    //on va crée une second constante pour aller dans le dossier _templates
    public const PATH_PARTIALS = self::PATH_VIEW . '_templates' . DS;
    //on déclare un titre par defaut
    public string $title = 'Papa Pizza';

    //on va déclarer un constructeur
    public function __construct(private string $name, private bool $is_complete = true)
    {
    }

    //méthode pour recuperer le chemin de la vue
    //'home/home'
    private function getRequirePath(): string
    {
        //on va explode le nom de la vue pour récupérer le dossier et le fichier
        $arr_name = explode('/', $this->name);
        //on récuper le 1 element
        $category = $arr_name[0];
        //on récuper le 2 element
        $name = $arr_name[1];
        //si je crée un template on ajoutera un _ devant le mot du fichier
        $name_prefix = $this->is_complete ? '' : '_';
        //reste plus qu a retouner le chemin de la vue
        return self::PATH_VIEW . $category . DS . $name_prefix . $name . '.html.php';
    }

    //on crée la methode de rendu de la vue
    public function render(array $view_data = [])
    {
        //on recuper les donneé d utilisateur
        $auth = AuthController::class;
        
        //si on desdonnée on les extait
        if (!empty($view_data)) {
            extract($view_data);
        };

        //mise en cache du contenu de la vue
        ob_start();
        //on importe le template header.html.php si la vue est complette
        if ($this->is_complete) {
            require self::PATH_PARTIALS . '_header.html.php';
        }

        //on imorte la vue
        require_once $this->getRequirePath();

        //on importe le template header.html.php si la vue est complette
        if ($this->is_complete) {
            require self::PATH_PARTIALS . '_footer.html.php';
        }
    //on libert le cahche
        ob_end_flush();
    }
}
