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

import React, { PropTypes, Component }  from 'react';
import Nav                            from 'react-bootstrap/lib/Nav';
import MenuItem                       from '../../markup/MenuItem';
import menu                           from '../../../../config/menu';

/**
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @author Benjamin Bieler <ben@benbieler.com>
 */
export default class Menu extends Component {

  static propTypes = {
    items:   PropTypes.array.isRequired,
    actions: PropTypes.object.isRequired
  };

  static contextTypes = {
    router: PropTypes.oneOfType([
      PropTypes.func,
      PropTypes.object
    ])
  };

  /**
   * When component has mounted call buildMenuItems action creator.
   *
   * @returns {void}
   */
  componentDidMount() {
    this.props.actions.buildMenuItems(menu);
  }

  /**
   * Simple helper to build a list of menu items.
   *
   * @param {Array.<Object>} items   The list of items to render.
   *
   * @returns {Array.<MenuItem>} List of menu items.
   */
  items(items) {
    return items.map((item, i) => {
      const u = item.url.slice(2);
      return <MenuItem label={item.label} url={item.url} key={i} isActive={this.context.router.isActive(u, '/' === u)} />;
    });
  }

  /**
   * Simple react component which builds the menu bar.
   *
   * @returns {React.Element} The component markup.
   */
  render() {
    const rItems = this.items(this.props.items);
    return 0 < rItems.length ? <Nav pullRight>{rItems}</Nav> : false;
  }
}
