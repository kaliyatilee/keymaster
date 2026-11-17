/**
 * A static illustration of the typing screen used on the landing page.
 * It shows how correct and incorrect characters are highlighted; it is not
 * a working typing test (that ships in Phase 3).
 */
const target = 'the quick brown fox jumps over the lazy dog';
const typed = 'the quick brwon fox ju';

export default function TypingPreview() {
    return (
        <figure
            className="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-6"
            aria-label="Example of the typing screen: correct letters in green, mistakes in red"
        >
            <div className="mb-4 flex items-center justify-between text-xs font-medium text-muted-foreground">
                <span>Example</span>
                <div className="flex gap-4" aria-hidden="true">
                    <span>
                        <span className="text-lg font-semibold text-foreground">
                            38
                        </span>{' '}
                        WPM
                    </span>
                    <span>
                        <span className="text-lg font-semibold text-foreground">
                            91%
                        </span>{' '}
                        accuracy
                    </span>
                </div>
            </div>
            <p
                className="font-mono text-xl leading-relaxed tracking-wide sm:text-2xl"
                aria-hidden="true"
            >
                {target.split('').map((char, index) => {
                    const state =
                        index < typed.length
                            ? typed[index] === char
                                ? 'correct'
                                : 'incorrect'
                            : index === typed.length
                              ? 'cursor'
                              : 'pending';

                    return (
                        <span
                            key={index}
                            className={
                                state === 'correct'
                                    ? 'text-typing-correct'
                                    : state === 'incorrect'
                                      ? 'rounded-sm bg-typing-incorrect/15 text-typing-incorrect underline decoration-2 underline-offset-4'
                                      : state === 'cursor'
                                        ? 'border-l-2 border-typing-cursor text-muted-foreground motion-safe:animate-pulse'
                                        : 'text-muted-foreground/60'
                            }
                        >
                            {char}
                        </span>
                    );
                })}
            </p>
            <div className="mt-5 h-1.5 overflow-hidden rounded-full bg-muted">
                <div
                    className="h-full rounded-full bg-primary"
                    style={{
                        width: `${(typed.length / target.length) * 100}%`,
                    }}
                />
            </div>
            <figcaption className="sr-only">
                Correct letters turn green, mistakes are marked in red, and your
                speed and accuracy update as you type.
            </figcaption>
        </figure>
    );
}
