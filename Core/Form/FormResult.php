<?php


namespace Core\Form;


class FormResult
{
    //gestioh des message dde reussite
    private FormSuccess $success_message;
    /**
     * methode qui permet de recuperer le message de reussite
     * @return FormSuccess $success_message
     * 
     */
    public function getSuccessMessage(): FormSuccess
    {
        return $this->success_message;
    }

    /**
     * methode qui permet de modifier le message de reussite
     * @return void
     * @param FormSuccess $success
     */
    public function addSuccess(FormSuccess $success): void
    {
        $this->success_message = $success;
    }

    /**
     * methode qui pverifie si le message de reussite est vide
     * @return bool
     * @return array
     */
    public function hasSuccess(): bool
    {
        return !empty($this->success_message);
    }
    //gestion des message d'echec
    private array $form_errors = [];

    public function getErrors(): array
    {
        return $this->form_errors;
    }

    /**
     * methode qui permet de modifier le message d'echec    
     * @param FormError $error
     * @return void
     */
    public function addError(FormError $error): void
    {
        $this->form_errors[] = $error;
    }

    /**
     * methode qui permet de verifier si le message d'echec est vide
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->form_errors);
    }

    

}
