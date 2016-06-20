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

import Nav from 'react-bootstrap/lib/Nav';
import MenuStore from '../../../../store/MenuStore';
import { buildMenuItems } from '../../../../actions/MenuActions';
import React from 'react';
import MenuItem from '../../markup/MenuItem';
import { connector, runAction } from 'sententiaregum-flux-container';
import UserStore from '../../../../store/UserStore';

/**
 * Configurable menu rendering component.
 *
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Menu extends React.Component {
  /**
   * Constructor.
   *
   * @param {Object} props Internal properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.state = {
      items: []
    };

    this.handle       = this._storeMenuItems.bind(this);
    this.authReloader = this._reEvaluateMenuItems.bind(this);
  }

  /**
   * Connects the component with the data store.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(MenuStore).useWith(this.handle);
    connector(UserStore).useWith(this.authReloader);

    this._reEvaluateMenuItems();
  }

  /**
   * Removes the hook to the menu store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(MenuStore).unsubscribe(this.handle);
    connector(UserStore).unsubscribe(this.authReloader);
  }

  /**
   * Creates a configurable menu component for bootstrap3.
   *
   * @returns {React.Element} Renders the menu bar.
   */
  render() {
    const items = this.state.items.map((item, i) => {
      const urlWithoutPrefix = item.url.slice(2);
      return <MenuItem
        label={item.label}
        url={item.url}
        key={i}
        isActive={this.context.router.isActive(urlWithoutPrefix, '/' === urlWithoutPrefix)} />;
    });

    let nav = false;
    if (0 < items.length) {
      nav = <Nav pullRight>{items}</Nav>;
    }

    return nav;
  }

  /**
   * Stores a new menu item.
   *
   * @returns {void}
   */
  _storeMenuItems() {
    this.setState({
      items: MenuStore.getState()
    });
  }

  /**
   * Reevaluates the menu items.
   *
   * @returns {void}
   * @private
   */
  _reEvaluateMenuItems() {
    runAction(buildMenuItems, [this.props.items]);
  }
}

Menu.propTypes = {
  items: React.PropTypes.array
};

Menu.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
