// Time ago helper
export function timeAgo(date: string | Date, lang: string = 'ru'): string {
  const now = new Date();
  const past = new Date(date);
  const diffMs = now.getTime() - past.getTime();
  const diffSec = Math.floor(diffMs / 1000);
  const diffMin = Math.floor(diffSec / 60);
  const diffHour = Math.floor(diffMin / 60);
  const diffDay = Math.floor(diffHour / 24);
  const diffWeek = Math.floor(diffDay / 7);
  const diffMonth = Math.floor(diffDay / 30);
  const diffYear = Math.floor(diffDay / 365);

  const translations = {
    ru: {
      justNow: 'только что',
      minutesAgo: (n: number) => `${n} ${n === 1 ? 'минуту' : n < 5 ? 'минуты' : 'минут'} назад`,
      hoursAgo: (n: number) => `${n} ${n === 1 ? 'час' : n < 5 ? 'часа' : 'часов'} назад`,
      daysAgo: (n: number) => `${n} ${n === 1 ? 'день' : n < 5 ? 'дня' : 'дней'} назад`,
      weeksAgo: (n: number) => `${n} ${n === 1 ? 'неделю' : n < 5 ? 'недели' : 'недель'} назад`,
      monthsAgo: (n: number) => `${n} ${n === 1 ? 'месяц' : n < 5 ? 'месяца' : 'месяцев'} назад`,
      yearsAgo: (n: number) => `${n} ${n === 1 ? 'год' : n < 5 ? 'года' : 'лет'} назад`,
    },
    en: {
      justNow: 'just now',
      minutesAgo: (n: number) => `${n} minute${n !== 1 ? 's' : ''} ago`,
      hoursAgo: (n: number) => `${n} hour${n !== 1 ? 's' : ''} ago`,
      daysAgo: (n: number) => `${n} day${n !== 1 ? 's' : ''} ago`,
      weeksAgo: (n: number) => `${n} week${n !== 1 ? 's' : ''} ago`,
      monthsAgo: (n: number) => `${n} month${n !== 1 ? 's' : ''} ago`,
      yearsAgo: (n: number) => `${n} year${n !== 1 ? 's' : ''} ago`,
    },
    az: {
      justNow: 'indicə',
      minutesAgo: (n: number) => `${n} dəqiqə əvvəl`,
      hoursAgo: (n: number) => `${n} saat əvvəl`,
      daysAgo: (n: number) => `${n} gün əvvəl`,
      weeksAgo: (n: number) => `${n} həftə əvvəl`,
      monthsAgo: (n: number) => `${n} ay əvvəl`,
      yearsAgo: (n: number) => `${n} il əvvəl`,
    }
  };

  const t = translations[lang as keyof typeof translations] || translations.ru;

  if (diffSec < 60) return t.justNow;
  if (diffMin < 60) return t.minutesAgo(diffMin);
  if (diffHour < 24) return t.hoursAgo(diffHour);
  if (diffDay < 7) return t.daysAgo(diffDay);
  if (diffWeek < 4) return t.weeksAgo(diffWeek);
  if (diffMonth < 12) return t.monthsAgo(diffMonth);
  return t.yearsAgo(diffYear);
}
