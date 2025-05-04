import React from "react";

function CurrencyFormatter({
  amount,
  currency = "USD",
  locale = "en-US",
}: {
  amount: number;
  currency: string;
  locale: string;
}) {
  return new Intl.NumberFormat(locale, {
    style: "currency",
    currency,
  }).format(amount);
}

export default CurrencyFormatter;
