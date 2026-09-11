const formatter = new Intl.NumberFormat('fr-FR', {
    maximumFractionDigits: 0,
});

/**
 * Formate un montant entier en FCFA : `12 500 F CFA`.
 * Source unique de formatage côté front.
 */
export function useCurrency() {
    const format = (amount: number): string => `${formatter.format(amount)}\u00A0F\u00A0CFA`;

    return { format };
}
