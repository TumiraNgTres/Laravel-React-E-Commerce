import React from "react";

type CurrencyFormatterProps = {
  amount: number;
  currency?: string;
  locale?: string;
};

function CurrencyFormatter({
  amount,
  currency = "USD",
  locale = "en-US",
}: CurrencyFormatterProps) {
  return new Intl.NumberFormat(locale, {
    style: "currency",
    currency,
  }).format(amount);
}

export default CurrencyFormatter;
