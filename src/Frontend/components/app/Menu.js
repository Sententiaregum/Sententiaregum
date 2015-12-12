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

import Navbar from 'react-bootstrap/lib/Navbar';
import NavBrand from 'react-bootstrap/lib/NavBrand';
import Nav from 'react-bootstrap/lib/Nav';
import NavItem from 'react-bootstrap/lib/NavItem';
import MenuStore from '../../store/MenuStore';
import MenuActions from '../../actions/MenuActions';
import React from 'react';
import Translate from 'react-translate-component';
import LanguageSwitcher from './widgets/LanguageSwitcher';

/**
 * Configurable menu rendering component.
 *
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Menu extends React.Component {
  /**
   * @type {Object}
   */
  static propTypes = {
    items: React.PropTypes.array
  };

  /**
   * Constructor.
   *
   * @param {Object} props
   */
  constructor(props) {
    super(props);

    this.cls   = 'Menu';
    this.state = {
      items: []
    };
  }

  /**
   * Connects the component with the data store.
   */
  componentDidMount() {
    MenuStore.addChangeListener(this.storeMenuItems.bind(this), this.cls);
    MenuActions.buildMenuItems(this.props.items);
  }

  /**
   * Removes the hook to the menu store.
   */
  componentWillUnmount() {
    MenuStore.removeChangeListener(this.storeMenuItems.bind(this), this.cls);
  }

  /**
   * Stores a new menu item.
   */
  storeMenuItems() {
    this.setState({
      items: MenuStore.getItems()
    });
  }

  /**
   * Creates a configurable menu component for bootstrap3.
   *
   * @returns {Navbar}
   */
  render() {
    const items = this.state.items.map((item, i) => {
      return <NavItem href={item.url} key={i}>
        <Translate component="option" content={item.label} />
      </NavItem>;
    });

    let nav;
    if (items.length > 0) {
      nav = <Nav right>{items}</Nav>;
    }

    return (
      <Navbar inverse fixedTop toggleNavKey={0} navToggle={false}>
        <NavBrand><a href="/#/">Sententiaregum</a></NavBrand>
        <Nav left>
          <LanguageSwitcher />
        </Nav>
        {nav}
      </Navbar>
    );
  }
}
