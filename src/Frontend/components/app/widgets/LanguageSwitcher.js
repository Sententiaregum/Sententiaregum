/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React, { Component, PropTypes }    from 'react';
import Translate                          from 'react-translate-component';
import NavDropdown                        from 'react-bootstrap/lib/NavDropdown';
import DropDownItem                       from '../markup/DropDownItem';

/**
 * Widget which changes the user locale.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 * @author Ben Bieler <ben@benbieler.com>
 */
export default class LanguageSwitcher extends Component {

  static propTypes = {
    actions: PropTypes.object.isRequired
  };

  static contextTypes = {
    store: React.PropTypes.object
  };

  /**
   * Renders the component.
   *
   * @returns {React.Element} React dom that contains the locale switcher
   */
  render() {

    const { store }  = this.context;
    const locales    = store.getState().locales;
    const localeKeys = Object.keys(locales.available);

    return (
      <NavDropdown
        eventKey={1}
        id="l10n-dropdown"
        title={<Translate content="menu.l10n" />}
      >
        {localeKeys.map((key, i) => <DropDownItem
          key={i}
          isActive={locales.currentLocale === key}
          onSelect={(k, e) => this._changeLocale(e)}
          displayName={locales.available[key]}
          id={key}
        />)}

      </NavDropdown>
    );
  }

  /**
   * Change handler for the locale.
   *
   * @param {Object} e Event object.
   *
   * @returns {void}
   */
  _changeLocale(e) {
    if (-1 === e.target.parentNode.className.indexOf('active')) {
      this.props.actions.changeLocale(e.target.id);
      this.forceUpdate();
    }

    e.preventDefault();
  }
}
