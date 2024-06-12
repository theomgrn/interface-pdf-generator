describe('Génération de PDF', () => {
    it('test 1 - génération de PDF OK', () => {
        cy.visit('http://127.0.0.1:8000/generate/pdf');

        // Entrer l'URL à convertir en PDF
        cy.get('#url').type('https://example.com');

        // Soumettre le formulaire
        cy.get('#generate-form').submit();

        // Vérifier que l'indicateur de chargement est affiché
        cy.get('#loading').should('be.visible');

        // Vérifier que le bouton est désactivé
        cy.get('#generate-btn').should('be.disabled');

        // Attendre et vérifier que le PDF est généré
        cy.request({
            url: 'http://127.0.0.1:8000/generate/pdf/submit',
            method: 'POST',
            form: true,
            body: {
                url: 'https://example.com'
            }
        }).then((response) => {
            // Vérifier que le type de contenu est PDF
            expect(response.headers['content-type']).to.eq('application/pdf');
            // Vérifier que le nom du fichier est correct (optionnel)
            expect(response.headers['content-disposition']).to.include('inline; filename="');
        });

        // Réactiver le bouton et masquer l'indicateur de chargement (simulation de la fin de génération)
        cy.get('#generate-btn').should('not.be.disabled');
        cy.get('#loading').should('not.be.visible');
    });

    it('test 2 - génération de PDF KO (limite atteinte)', () => {
        // Simuler que l'utilisateur a atteint la limite de génération de PDF
        cy.intercept('POST', '/generate/pdf/submit', {
            statusCode: 302,
            headers: {
                location: '/upgrade_subscription'
            }
        });

        cy.visit('http://127.0.0.1:8000/generate/pdf');

        // Entrer l'URL à convertir en PDF
        cy.get('#url').type('https://example.com');

        // Soumettre le formulaire
        cy.get('#generate-form').submit();

        // Vérifier que l'utilisateur est redirigé vers la page d'abonnement
        cy.url().should('include', '/upgrade_subscription');
    });
});
