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
import React from 'react';
import MenuItem from '../../markup/MenuItem';
import { subscribeStores } from 'sententiaregum-flux-react';
import menuStore from '../../../../store/menuStore';

/**
 * Simple helper to build a list of menu items.
 *
 * @param {Array.<Object>} items   The list of items to render.
 * @param {Object.<*>}     context The component tree context.
 *
 * @returns {Array.<MenuItem>} List of menu items.
 */
const items = (items, context) => items.map((item, i) => {
  const u = item.url.slice(2);
  return <MenuItem label={item.label} url={item.url} key={i} isActive={context.router.isActive(u, '/' === u)} />;
});

/**
 * Simple react component which builds the menu bar.
 *
 * @param {Object.<*>} props   The component properties.
 * @param {Object.<*>} context The component tree context.
 *
 * @returns {React.Element} The component markup.
 */
const Menu = (props, context) => {
  const rItems = items(props.items, context);
  return 0 < rItems.length ? <Nav pullRight>{rItems}</Nav> : false;
};

Menu.propTypes = {
  items: React.PropTypes.array
};

Menu.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};

export default subscribeStores(Menu, {
  'items': [menuStore, 'items']
});
