/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React, { Component, PropTypes  } from 'react';
import Translate                        from 'react-translate-component';
import NavDropdown                      from 'react-bootstrap/lib/NavDropdown';
import LoadingDropDown                  from '../markup/LoadingDropDown';
import DropDownItem                     from '../markup/DropDownItem';
import Locale                           from '../../../util/http/Locale';

/**
 * Widget which changes the user locale.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LanguageSwitcher extends Component {

  static PropTypes = {
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

    const {store}    = this.context;
    let locales      = store.getState().locales;
    const localeKeys = Object.keys(locales);

    return (
      <NavDropdown
        eventKey={1}
        id="l10n-dropdown"
        title={<Translate content="menu.l10n" />}
      >
        {0 === localeKeys.length
          ? <LoadingDropDown translationContent="menu.l10n_loading" />
          : localeKeys.map((key, i) => <DropDownItem
              key={i}
              isActive={Locale.getLocale() === key}
              onSelect={(k, e) => this._changeLocale(e)}
              displayName={locales[key]}
              id={key}
            />)
        }
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
