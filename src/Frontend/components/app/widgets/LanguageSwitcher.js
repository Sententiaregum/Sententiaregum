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
import Translate from 'react-translate-component';
import LocaleActions from '../../../actions/LocaleActions';
import LocaleStore from '../../../store/LocaleStore';
import { Locale } from '../../../util/http/facade/HttpServices';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';
import LoadingDropDown from '../markup/LoadingDropDown';
import DropDownItem from '../markup/DropDownItem';

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

    this.handle = this._refreshLocales.bind(this);
  }

  /**
   * Connects the component with the data store.
   *
   * @returns {void}
   */
  componentDidMount() {
    LocaleStore.addChangeListener(this.handle, 'Locale');
    LocaleActions.loadLanguages();
  }

  /**
   * Removes the hook to the locale store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    LocaleStore.removeChangeListener(this.handle, 'Locale');
  }

  /**
   * Renders the component.
   *
   * @returns {React.Element} React dom that contains the locale switcher
   */
  render() {
    const translatedMenuItem = <Translate content="menu.l10n" />,
        localeKeys           = Object.keys(this.state.locales),
        languageItems        = 0 === localeKeys.length
          ? <LoadingDropDown translationContent="menu.l10n_loading" />
          : localeKeys.map(key => this._buildDropDown(key));

    return (
      <NavDropdown
        eventKey={1}
        id="l10n-dropdown"
        title={translatedMenuItem}
      >
        {languageItems}
      </NavDropdown>
    );
  }

  /**
   * Refreshes locale list.
   *
   * @returns {void}
   */
  _refreshLocales() {
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
  _changeLocale(e) {
    if (-1 === e.target.parentNode.className.indexOf('active')) {
      LocaleActions.changeLocale(e.target.id);
      this.forceUpdate();
    }

    e.preventDefault();
  }

  /**
   * Builds a dropdown item by its locale key.
   *
   * @param {string} key Locale key.
   *
   * @returns {React.Element} The markup.
   */
  _buildDropDown(key) {
    const displayName = this.state.locales[key],
        isActive        = Locale.getLocale() === key;

    return <DropDownItem
      key={key}
      isActive={isActive}
      onSelect={(e) => this._changeLocale(e)}
      displayName={displayName}
      id={key}
    />;
  }
}
