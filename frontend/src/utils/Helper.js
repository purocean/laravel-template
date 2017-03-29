const textTree = (data, level = 1) => {
  return data.map(item => {
    let padding = new Array(level + 1).join('  ') +
        (item.children && item.children.length > 1 ? '+ ' : '- ')
    return `${padding}${item.title}\n` + textTree(item.children, level + 1)
  }).join('')
}

export default { textTree }
