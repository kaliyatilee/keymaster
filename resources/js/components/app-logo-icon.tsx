import type { SVGAttributes } from 'react';

/** KeyMaster mark: a keycap with a "K". */
export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                fillRule="evenodd"
                d="M8 3h24a6 6 0 0 1 6 6v18a6 6 0 0 1-6 6v1a3 3 0 0 1-3 3H11a3 3 0 0 1-3-3v-1a6 6 0 0 1-6-6V9a6 6 0 0 1 6-6Zm7 8v18h4v-6l2-2 5 8h5l-7-11 6-7h-5l-6 7.4V11h-4Z"
            />
        </svg>
    );
}
