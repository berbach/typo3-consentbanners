window.addEventListener('load', () => {
    document.querySelectorAll('.form-control-clearable button.close').forEach(el =>
        el.addEventListener('click', () => {
            const input = el.parentElement.querySelector('input')
            input.value = ''
            input.focus()
        })
    )

    document.querySelector('#main-form')?.addEventListener('submit', e => {
        e.preventDefault()

        const oldParameters = window.location.search.replace(/^\?/, '').split('&').map(arg => {
            const [key, value] = arg.split('=').map(a => decodeURIComponent(a).replace(/^[^[]+\[([^\]]+)]$/, '$1'))
            return [key, value];
        })
        const filteredId = oldParameters.filter(prm => prm[0] === 'id')

        const data = {
            type: oldParameters.filter(prm => prm[0] === 'type')[0][1],
            id: filteredId.length > 0 ? filteredId[0][1] : undefined,
            name: e.target.querySelector('[name=name]')?.value,
            description: e.target.querySelector('[name=description]')?.value,

            old_selected_modules: e.target.querySelector('[name=old_selected_modules]')?.value.split(',')
                ?.filter(s => !!s?.match(/\d/)).map(s => Number(s)),

            selected_modules: Array.from(e.target.querySelectorAll('[name=selected_modules] option'))
                ?.map(el => el?.value).filter(s => !!s?.match(/\d/)).map(s => Number(s)),

            unselected_modules: Array.from(e.target.querySelectorAll('[name=unselected_modules] option'))
                ?.map(el => el?.value).filter(s => !!s?.match(/\d/)).map(s => Number(s)),

            category: e.target.querySelector('[name=category]')?.value,

            accepted_script: e.target.querySelector('[name=accepted_script]')?.value,
            rejected_script: e.target.querySelector('[name=rejected_script]')?.value,
        }

        if (!arraysEqual(data.old_selected_modules, data.selected_modules)) {
            data.removed_modules = data.old_selected_modules?.filter(id => !data.selected_modules?.includes(id))
        }

        let url = e.target.action
        for (let key in data) {
            if (!data[key]) continue
            if (Array.isArray(data[key]) && data[key].join(',') === '') continue

            url += '&tx_cookiebanner_web_cookiebannermanagement%5B'
            url += encodeURIComponent(key)
            url += '%5D='
            url += encodeURIComponent(Array.isArray(data[key]) ? data[key].join(',') : data[key])
        }

        window.location.href = url;
    })

    const selectedModulesElement = document.querySelector('[name=selected_modules]')
    const unselectedModulesElement = document.querySelector('[name=unselected_modules]')

    let allUnselectedOptions = Array.from(unselectedModulesElement?.querySelectorAll('option') || [])

    document.querySelector('[name=unselected_modules]')?.addEventListener('input', e => {
        allUnselectedOptions = allUnselectedOptions.filter(el => el.value !== e.target.value)
        moveSelectedOption(e.target, selectedModulesElement)
    })

    document.querySelector('.t3js-btn-removeoption')?.addEventListener('click', () => {
        allUnselectedOptions.push(
            selectedModulesElement?.querySelector(`option[value="${selectedModulesElement.value}"]`)
        )
        moveSelectedOption(selectedModulesElement, unselectedModulesElement)
    })

    document.querySelector('.t3js-btn-moveoption-up')?.addEventListener('click', () => {
        moveSelectedOption(selectedModulesElement, selectedModulesElement, 'up')
    })

    document.querySelector('.t3js-btn-moveoption-down')?.addEventListener('click', () => {
        moveSelectedOption(selectedModulesElement, selectedModulesElement, 'down')
    })

    document.querySelector('.t3js-btn-moveoption-top')?.addEventListener('click', () => {
        moveSelectedOption(selectedModulesElement, selectedModulesElement, 'top')
    })

    document.querySelector('.t3js-btn-moveoption-bottom')?.addEventListener('click', () => {
        moveSelectedOption(selectedModulesElement, selectedModulesElement)
    })

    document.querySelector('.t3js-formengine-multiselect-filter-textfield')?.addEventListener('input', e => {
        unselectedModulesElement.innerHTML = ''
        allUnselectedOptions.forEach(el => {
            if (el.innerText.toLowerCase().includes(e.target.value.toLowerCase()))
                unselectedModulesElement.append(el)
        })
    })

    document.querySelectorAll('.nav-tabs li a')?.forEach(el =>
        el?.addEventListener('click', tabClickHandler)
    )
    tabClickHandler()
})

function tabClickHandler(e) {
    const id = e ? e.target.href.replace(/.+?#tab-/, '') : '1'
    document.querySelectorAll('[class*=tab-]:not(.tab-content)').forEach(el => el.style.display = 'none')
    document.querySelectorAll('.tab-' + id).forEach(el => el.style.display = '')
}

function arraysEqual(a, b) {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (a.length !== b.length) return false;

    for (let i = 0; i < a.length; ++i) {
        if (a[i] !== b[i]) return false;
    }
    return true;
}

function moveSelectedOption(from, to, direction) {
    const val = from?.value
    const optionRef = from?.querySelector(`option[value="${val}"]`)
    if (!optionRef) return
    const option = optionRef.cloneNode(true)

    const elementBefore = optionRef.previousElementSibling,
        elementAfter = optionRef.nextElementSibling

    if ((direction === 'up' && !elementBefore) || (direction === 'down' && !elementAfter)) return

    optionRef.remove()

    switch (direction) {
        case 'up':
            elementBefore?.insertAdjacentElement('beforebegin', option)
            break
        case 'down':
            elementAfter?.insertAdjacentElement('afterend', option)
            break
        case 'top':
            to.prepend(option)
            break
        case 'bottom':
        default:
            to.append(option)
            break
    }
    to.value = val
}


TYPO3.settings.FormEngine = {
    "formName": "editform"
}

requirejs(['TYPO3\/CMS\/T3editor\/T3editor'], function (T3editor) {
    T3editor.observeEditorCandidates()
});
