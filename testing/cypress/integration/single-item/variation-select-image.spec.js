// / <reference types="cypress" />
context("Variation Select - Image", () =>
{
    /*
    Artikel-ID 144: Ein Attribut - Bild - Nicht kaufbar angezeigt

    1130 - 1133 angezeigt
    1130  (rot) nicht kaufbar
    1131(schwarz) und 1132 (lila) mit Bild von Variante
    1130 (rot) und 1133(blau) mit Bild vom Attribut
    */

    it("should check for saleability of available variations of item 144", () =>
    {
        cy.visit("/variantenauswahl/ein-attribut-bild-nicht-kaufbar-angezeigt_144_1130/");
        isNotSaleable();

        cy.get(".variation-select .text-muted").should("contain", "Farbauswahl - Bild - Nicht gruppiert");
        cy.get(".variation-select .text-muted + b").should("contain", "rot");

        cy.get(".v-s-box").eq(0).should("exist");

        cy.get(".v-s-box").eq(1).click();
        cy.get(".variation-select .text-muted + b").should("contain", "schwarz");
        isSaleable();

        cy.get(".v-s-box").eq(2).click();
        cy.get(".variation-select .text-muted + b").should("contain", "lila");
        isSaleable();

        cy.get(".v-s-box").eq(3).click();
        cy.get(".variation-select .text-muted + b").should("contain", "blau");
        cy.get(".v-s-box").eq(4).should("not.exist");
    });

    it("should check for correct images of all variants of item 144", () =>
    {
        cy.visit("/variantenauswahl/ein-attribut-bild-nicht-kaufbar-angezeigt_144_1131/");

        cy.get(".single-carousel img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/middle/schwarz.jpg');
        cy.get(".v-s-box").eq(1).find("img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/preview/schwarz.jpg');
        
        cy.get(".v-s-box").eq(2).click();
        cy.get(".single-carousel img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/middle/lila.jpg');
        cy.get(".v-s-box").eq(2).find("img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/preview/lila.jpg');
        
        cy.get(".v-s-box").eq(3).click();
        cy.get(".single-carousel .owl-item.active img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/middle/lila.jpg');
        cy.get(".v-s-box").eq(3).find("img").should('have.attr', 'src', '/images/produkte/grp/attr_20.png');
        
        cy.get(".v-s-box").eq(0).click();
        cy.get(".single-carousel .owl-item.active img").should('have.attr', 'src', 'https://cdn02.plentymarkets.com/2x3z2pucy2z9/item/images/144/middle/lila.jpg');
        cy.get(".v-s-box").eq(0).find("img").should('have.attr', 'src', '/images/produkte/grp/attr_17.png');
        
    });

    function isSaleable()
    {
        cy.get(".add-to-basket-container > button").should("not.have.class", "disabled");
    }
    function isNotSaleable()
    {
        cy.get(".add-to-basket-container > button").should("have.class", "disabled");
    }
});
