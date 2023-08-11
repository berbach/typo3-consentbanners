window.addEventListener('load', async () => {
    const TYPO3 = {
        Modal: await new Promise(r => requirejs(['TYPO3\/CMS\/Backend\/Modal'], u => r(u))),
        Severity: await new Promise(r => requirejs(['TYPO3\/CMS\/Backend\/Severity'], u => r(u))),
    }

    let unedited = true

    document.querySelectorAll('input, select, textarea').forEach(el =>
        el?.addEventListener('input', () => {
            unedited = false
        })
    )

    document.querySelector('.btn[title=Close]')?.addEventListener('click', e => {
        if (unedited) return true
        e.preventDefault()
        TYPO3.Modal.confirm(
            'Do you want to close without saving?',
            'You currently have unsaved changes. Are you sure you want to discard these changes?',
            TYPO3.Severity.warning,
            [{
                text: 'No, I will continue editing',
                trigger: () => TYPO3.Modal.dismiss(),
                btnClass: 'btn-default'
            }, {
                text: 'Yes, discard my changes',
                trigger: () => {
                    TYPO3.Modal.dismiss()
                    window.location.href = document.querySelector('.btn[title=Close]').href
                },
                btnClass: 'btn-default'
            }, {
                text: 'Save and close',
                trigger: () => {
                    TYPO3.Modal.dismiss()
                    document.querySelector('.btn[title=Save]').click()
                },
                active: true,
                btnClass: 'btn-warning'
            }]
        )
    })

    document.querySelector('.btn[title=Delete]')?.addEventListener('click', e => {
        e.preventDefault()
        TYPO3.Modal.confirm(
            'Delete this record?',
            'Are you sure you want to delete this record?',
            TYPO3.Severity.warning,
            [{
                text: 'Cancel',
                trigger: () => TYPO3.Modal.dismiss(),
                btnClass: 'btn-default'
            }, {
                text: 'Yes, delete this record',
                trigger: () => {
                    TYPO3.Modal.dismiss()
                    window.location.href = document.querySelector('.btn[title=Delete]').href
                },
                active: true,
                btnClass: 'btn-warning'
            }]
        )
    })
})
