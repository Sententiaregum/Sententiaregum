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

import React, { Component } from 'react';
import Translate from 'react-translate-component';
import localeStore from '../../../store/localeStore';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';
import LoadingDropDown from '../markup/LoadingDropDown';
import DropDownItem from '../markup/DropDownItem';
import { runAction } from 'sententiaregum-flux-container';
import localeActions from '../../../actions/localeActions';
import { GET_LOCALES, CHANGE_LOCALE } from '../../../constants/Locale';
import { subscribeStores } from 'sententiaregum-flux-react';
import Locale from '../../../util/http/Locale';

/**
 * Widget which changes the user locale.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LanguageSwitcher extends Component {
  /**
   * Lifecycle hook triggers the action to gather locale information.
   *
   * @returns {void}
   */
  componentDidMount() {
    runAction(GET_LOCALES, localeActions, []);
  }

  /**
   * Renders the component.
   *
   * @returns {React.Element} React dom that contains the locale switcher
   */
  render() {
    const localeKeys = Object.keys(this.props.locales);

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
              displayName={this.props.locales[key]}
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
      runAction(CHANGE_LOCALE, localeActions, [e.target.id]);
      this.forceUpdate();
    }

    e.preventDefault();
  }
}

LanguageSwitcher.propTypes = {
  locales: React.PropTypes.object
};

LanguageSwitcher.defaultProps = {
  locales: {}
};

export default subscribeStores(LanguageSwitcher, {
  locales: [localeStore, 'locales']
});
