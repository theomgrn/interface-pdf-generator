describe('Formulaire d\'Inscription', () => {
    it('test 1 - enregistrement OK', () => {
        cy.visit('http://127.0.0.1:8000/register');

        // Entrer l'email, le mot de passe et cocher la case
        cy.get('input[name="registration_form[email]"]').type('newuser@mail.com');
        cy.get('input[name="registration_form[plainPassword]"]').type('strongpassword');
        cy.get('input[name="registration_form[agreeTerms]"]').check();

        // Soumettre le formulaire
        cy.get('button[type="submit"]').click();

        // Vérifier que l'utilisateur est inscrit et connecté
        // Assurez-vous d'avoir un message ou une redirection spécifique pour valider l'inscription
        cy.contains('You have successfully registered.').should('exist');
    });

    it('test 2 - enregistrement KO', () => {
        cy.visit('http://127.0.0.1:8000/register');

        // Entrer un email invalide, un mot de passe faible et ne pas cocher la case
        cy.get('input[name="registration_form[email]"]').type('invaliduser@mail');
        cy.get('input[name="registration_form[plainPassword]"]').type('weakpassword');
        // Ne pas cocher la case d'acceptation des termes

        // Soumettre le formulaire
        cy.get('button[type="submit"]').click();

        // Vérifier que les messages d'erreur sont affichés
        cy.contains('This value is not a valid email address.').should('exist');
        cy.contains('Your password should be at least 6 characters').should('exist');
        cy.contains('You should agree to our terms.').should('exist');
    });
});
