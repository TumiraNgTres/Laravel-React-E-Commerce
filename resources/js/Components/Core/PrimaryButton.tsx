import { ButtonHTMLAttributes } from "react";

export default function PrimaryButton({
  className = "",
  disabled,
  children,
  ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
  return (
    <button
      {...props}
      className={
        `inline-flex items-center rounded-md border border-transparent bg-violet-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-violet-800 focus:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 active:bg-violet-900 dark:bg-violet-200 dark:text-violet-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-violet-800 dark:active:bg-violet-300 ${
          disabled && "opacity-25"
        } ` + className
      }
      disabled={disabled}
    >
      {children}
    </button>
  );
}
