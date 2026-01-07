import { format, formatDistanceToNow } from "date-fns";
import { id } from "date-fns/locale";

export const formatDate = (date) =>
  format(new Date(date), "dd MMMM yyyy", { locale: id });

export const formatDateTime = (date) =>
  format(new Date(date), "dd MMMM yyyy, HH:mm", { locale: id });

export const timeAgo = (date) =>
  formatDistanceToNow(new Date(date), { addSuffix: true, locale: id });

export const toMysqlDate = (date) => format(new Date(date), "yyyy-MM-dd");
