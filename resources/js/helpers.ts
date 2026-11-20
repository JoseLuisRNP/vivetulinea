export const roundedPoints = (points: number) => {
  return Math.max(Math.round(points * 2) / 2, 0);
};

export const getCurrentTimeOfDay = (): string => {
  const hour = new Date().getHours();
  if (hour < 10) return 'Desayuno';
  if (hour < 12) return 'Media mañana';
  if (hour < 16) return 'Almuerzo';
  if (hour < 19) return 'Merienda';
  return 'Cena';
};

/**
 * Days are local calendar days ("YYYY-MM-DD"), never UTC instants: the
 * server keeps them as-is, so they don't shift when the user is in another
 * timezone. Don't use toISOString() for days.
 */
export const toLocalDateString = (date: Date): string => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

/**
 * Parse a day sent by the server or found in the URL as a local Date at
 * midnight. Accepts "YYYY-MM-DD" (also the date part of a serialized date
 * column) and falls back to today for anything else.
 */
export const parseLocalDate = (value?: string | null): Date => {
  const match = value?.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (match) {
    return new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
  }
  const today = new Date();
  return new Date(today.getFullYear(), today.getMonth(), today.getDate());
};

export const todayLocalDateString = (): string => toLocalDateString(new Date());

/**
 * Store the browser timezone in a cookie so the server knows the user's
 * "today" on requests that don't carry an explicit day.
 */
export const rememberTimezone = (): void => {
  try {
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (!timezone) return;
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `tz=${encodeURIComponent(timezone)}; path=/; max-age=31536000; SameSite=Lax${secure}`;
  } catch {
    // Without the cookie the server falls back to Europe/Madrid.
  }
};
