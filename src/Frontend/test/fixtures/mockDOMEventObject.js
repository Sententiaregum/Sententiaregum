/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

function noop() {
}

/**
 * Factory for DOM event objects.
 *
 * @param {Object} attrs Attributes being attached to the object.
 *
 * @returns {Object} A mocked DOM event object.
 */
export default attrs => {
  const obj = {
    preventDefault: noop
  };

  if (attrs && Object.keys(attrs).length > 0) {
    obj.target = {
      getAttribute: name => attrs[name]
    };

    Object.assign(obj.target, attrs);
  }

  return obj;
};
