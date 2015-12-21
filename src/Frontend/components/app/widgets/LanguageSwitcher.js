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

import React from 'react';
import MenuItem from 'react-bootstrap/lib/MenuItem';
import Translate from 'react-translate-component';
import LocaleActions from '../../../actions/LocaleActions';
import LocaleStore from '../../../store/LocaleStore';
import {Locale} from '../../../util/http/facade/HttpServices';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';

/**
 * Widget which changes the user locale.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LanguageSwitcher extends React.Component {
  /**
   * Constructor.
   */
  constructor(props) {
    super(props);

    this.state = {
      locales: {}
    };
  }

  /**
   * Connects the component with the data store.
   */
  componentDidMount() {
    LocaleStore.addChangeListener(this.refreshLocales.bind(this), 'Locale');
    LocaleActions.loadLanguages();
  }

  /**
   * Removes the hook to the locale store.
   */
  componentWillUnmount() {
    LocaleStore.removeChangeListener(this.refreshLocales.bind(this), 'Locale');
  }

  /**
   * Refreshes locale list.
   */
  refreshLocales() {
    this.setState({
      locales: LocaleStore.getAllLocales()
    });
  }

  /**
   * Change handler for the locale.
   *
   * @param {Object} e
   */
  changeLocale(e) {
    LocaleActions.changeLocale(e.target.id);
    this.forceUpdate();

    e.preventDefault();
  }

  /**
   * Renders the component.
   *
   * @returns {React.DOM}
   */
  render() {
    const translatedMenuItem = <Translate content="menu.l10n" />,
      localeKeys = Object.keys(this.state.locales);
    let languageItems;

    if (localeKeys.length === 0) {
      languageItems = (
        <MenuItem eventKey="1.1">
            <span className="loading">
              <Translate content="menu.l10n_loading" />
            </span>
        </MenuItem>
      );
    } else {
      languageItems = localeKeys.map((key) => {
        let displayName = this.state.locales[key], className;
        if (Locale.getLocale() === key) {
          className = 'active';
        }

        return (
          <MenuItem
            eventKey={key}
            key={key}
            className={className}
            onSelect={this.changeLocale.bind(this)}
            id={key}
          >
            {displayName}
          </MenuItem>
        );
      });
    }

    return (
      <NavDropdown eventKey={1} id="l10n-dropdown" title={translatedMenuItem}>
        {languageItems}
      </NavDropdown>
    );
  }
}
