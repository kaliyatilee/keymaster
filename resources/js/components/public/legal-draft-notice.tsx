/**
 * Shown on the legal pages until they have been reviewed by a lawyer for
 * the launch countries. Remove only after that review.
 */
export default function LegalDraftNotice() {
    return (
        <p
            role="note"
            className="rounded-xl border border-xp/40 bg-xp/10 p-4 text-sm text-xp-foreground dark:text-xp"
        >
            Draft for review. This page describes how KeyMaster is designed to
            handle your information. It has not yet been reviewed by a lawyer
            and will be updated before public launch.
        </p>
    );
}
