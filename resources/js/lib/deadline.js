/**
 * Formats a due date into a human-readable relative time string and a
 * color hint, mirroring the diffForHumans() logic previously duplicated
 * across student/tasks/{index,show}.blade.php.
 */
export function formatDeadline(dueDate) {
  if (!dueDate) {
    return { text: 'Tidak ada deadline', color: 'gray', isOverdue: false }
  }

  const due = new Date(dueDate)
  const now = new Date()
  const diffMs = due - now
  const isOverdue = diffMs < 0
  const absMs = Math.abs(diffMs)

  const minutes = Math.floor(absMs / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  let text
  let color

  if (isOverdue) {
    text = days > 0 ? `Terlambat ${days} hari` : hours > 0 ? `Terlambat ${hours} jam` : `Terlambat ${Math.max(1, minutes)} menit`
    color = 'red'
  } else if (days > 3) {
    text = `${days} hari lagi`
    color = 'green'
  } else if (days > 0) {
    text = `${days} hari ${hours % 24} jam lagi`
    color = 'yellow'
  } else if (hours > 0) {
    text = `${hours} jam ${minutes % 60} menit lagi`
    color = hours <= 3 ? 'red' : 'yellow'
  } else {
    text = `${Math.max(1, minutes)} menit lagi`
    color = 'red'
  }

  return { text, color, isOverdue }
}
