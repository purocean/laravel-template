const textTree = (data, level = 1) => {
  if (!data || data.length === 0) {
    return ''
  }

  return data.map(item => {
    let padding = new Array(level + 1).join('  ') +
        (item.children && item.children.length > 0 ? '+ ' : '- ')
    return `${padding}${item.title}\n` + textTree(item.children, level + 1)
  }).join('')
}

export default { textTree }
