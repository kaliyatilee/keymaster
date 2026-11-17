/**
 * Whole years between a YYYY-MM-DD date of birth and `on`.
 * Returns null for empty or invalid input. Used only to decide which
 * registration fields to show; the server makes the real decision.
 */
export function ageOn(dateOfBirth: string, on: Date): number | null {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(dateOfBirth);

    if (!match) {
        return null;
    }

    const year = Number(match[1]);
    const month = Number(match[2]);
    const day = Number(match[3]);

    if (month < 1 || month > 12 || day < 1 || day > 31) {
        return null;
    }

    let age = on.getFullYear() - year;
    const beforeBirthday =
        on.getMonth() + 1 < month ||
        (on.getMonth() + 1 === month && on.getDate() < day);

    if (beforeBirthday) {
        age -= 1;
    }

    return age >= 0 ? age : null;
}
