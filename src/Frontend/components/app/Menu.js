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
import NavItem from 'react-bootstrap/lib/NavItem';
import MenuStore from '../../store/MenuStore';
import MenuActions from '../../actions/MenuActions';
import React from 'react';
import Translate from 'react-translate-component';

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

    this.cls   = 'Menu';
    this.state = {
      items: []
    };

    this.handle = this._storeMenuItems.bind(this);
  }

  /**
   * Connects the component with the data store.
   *
   * @returns {void}
   */
  componentDidMount() {
    MenuStore.addChangeListener(this.handle, this.cls);
    MenuActions.buildMenuItems(this.props.items);
  }

  /**
   * Removes the hook to the menu store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    MenuStore.removeChangeListener(this.handle, this.cls);
  }

  /**
   * Creates a configurable menu component for bootstrap3.
   *
   * @returns {React.Element} Renders the menu bar.
   */
  render() {
    const items = this.state.items.map((item, i) => {
      return (
        <NavItem href={item.url} key={i}>
          <Translate content={item.label} />
        </NavItem>
      );
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
      items: MenuStore.getItems()
    });
  }
}

Menu.propTypes = {
  items: React.PropTypes.array
};
