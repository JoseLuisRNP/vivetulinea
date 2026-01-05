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
