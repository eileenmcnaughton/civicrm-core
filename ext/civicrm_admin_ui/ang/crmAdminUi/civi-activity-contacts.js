(function (CRM) {
  const ts = CRM.ts('civicrm_admin_ui');

  // Above this many contacts only the count is shown, so bulk mailings never have their recipients fetched
  const MAX_LISTED_CONTACTS = 10;

  // Every element connected in the same tick joins one batch, fetched with a single api call
  let openBatch = null;

  const fetchActivityContacts = (activityId) => {
    if (!openBatch) {
      const batch = {activityIds: new Set()};
      batch.records = new Promise((resolve) => setTimeout(resolve)).then(() => {
        openBatch = null;
        return CRM.api4('ActivityContact', 'get', {
          select: ['activity_id', 'record_type_id:name', 'contact_id', 'contact_id.display_name'],
          where: [
            ['activity_id', 'IN', [...batch.activityIds]],
            ['record_type_id:name', 'IN', ['Activity Targets', 'Activity Assignees']],
          ],
          orderBy: {'contact_id.sort_name': 'ASC'},
        });
      });
      openBatch = batch;
    }
    openBatch.activityIds.add(activityId);
    return openBatch.records.then((records) => records.filter((record) => record.activity_id === activityId));
  };

  /**
   * <civi-activity-contacts activity-id="1234" type="Activity Targets" contact-count="3">
   */
  class CiviActivityContacts extends HTMLElement {

    /* jshint ignore: start */
    static observedAttributes = ['activity-id'];
    /* jshint ignore: end */

    connectedCallback() {
      this.fetchAndRender();
    }

    attributeChangedCallback(name) {
      if (name === 'activity-id' && this.isConnected && this.activityId) {
        this.fetchAndRender();
      }
    }

    get activityId() {
      return parseInt(this.getAttribute('activity-id')) || null;
    }

    get type() {
      return this.getAttribute('type');
    }

    get contactCount() {
      return parseInt(this.getAttribute('contact-count')) || 0;
    }

    async fetchAndRender() {
      if (this.contactCount > MAX_LISTED_CONTACTS) {
        this.innerText = ts('%1 contacts', {1: this.contactCount});
        return;
      }
      this.renderLoading();
      if (!this.activityId) {
        return;
      }
      const records = await fetchActivityContacts(this.activityId);
      this.render(records
        // Contacts the user cannot see come back without a name; leave them out
        .filter((record) => record['record_type_id:name'] === this.type && record['contact_id.display_name'])
        .map((record) => ({id: record.contact_id, display_name: record['contact_id.display_name']}))
      );
    }

    renderLoading() {
      // One placeholder per contact so the row is already at its final height
      const placeholder = '<li><span class="crm-search-loading-placeholder"></span></li>';
      this.innerHTML = `<ul>${placeholder.repeat(this.contactCount || 1)}</ul>`;
    }

    render(contacts) {
      if (!contacts.length) {
        this.innerText = ts('(none)');
        return;
      }
      const list = document.createElement('ul');
      list.append(...contacts.map((contact) => {
        const anchor = document.createElement('a');
        anchor.href = CRM.url('civicrm/contact/view', {reset: 1, cid: contact.id});
        anchor.target = '_blank';
        anchor.innerText = contact.display_name;
        const item = document.createElement('li');
        item.append(anchor);
        return item;
      }));
      this.replaceChildren(list);
    }
  }

  customElements.define('civi-activity-contacts', CiviActivityContacts);

})(CRM);
