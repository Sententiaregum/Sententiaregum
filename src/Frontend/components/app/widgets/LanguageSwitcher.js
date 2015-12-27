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
import { Locale } from '../../../util/http/facade/HttpServices';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';

/**
 * Widget which changes the user locale.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LanguageSwitcher extends React.Component {
  /**
   * Constructor.
   *
   * @param {Array} props List of component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.state = {
      locales: {}
    };
  }

  /**
   * Connects the component with the data store.
   *
   * @returns {void}
   */
  componentDidMount() {
    LocaleStore.addChangeListener(this.refreshLocales.bind(this), 'Locale');
    LocaleActions.loadLanguages();
  }

  /**
   * Removes the hook to the locale store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    LocaleStore.removeChangeListener(this.refreshLocales.bind(this), 'Locale');
  }

  /**
   * Refreshes locale list.
   *
   * @returns {void}
   */
  refreshLocales() {
    this.setState({
      locales: LocaleStore.getAllLocales()
    });
  }

  /**
   * Change handler for the locale.
   *
   * @param {Object} e Event object
   *
   * @returns {void}
   */
  changeLocale(e) {
    LocaleActions.changeLocale(e.target.id);
    this.forceUpdate();

    e.preventDefault();
  }

  /**
   * Renders the component.
   *
   * @returns {React.DOM} React dom that contains the locale switcher
   */
  render() {
    const translatedMenuItem = <Translate content="menu.l10n" />,
        localeKeys = Object.keys(this.state.locales);
    let languageItems;

    if (0 === localeKeys.length) {
      languageItems = (
        <MenuItem eventKey="1.1">
            <span className="loading">
              <Translate content="menu.l10n_loading" />
            </span>
        </MenuItem>
      );
    } else {
      languageItems = localeKeys.map((key) => {
        const displayName = this.state.locales[key];
        let className;
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
